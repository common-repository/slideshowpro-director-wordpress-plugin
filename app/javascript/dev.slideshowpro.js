/***
* Script: SlideShowPro DirectorJs
* Desc. Javascript API Bridge for Director
* 1.5 and up
* Created By: Brad Daily
* Copyright ©2010 Bradley Boy Productions, LLC
* Copyright ©2010 Dominey Designs, LLC
* License http://slideshowpro.net
*/

if(typeof(console) === "undefined" || typeof(console.log) === "undefined")
    var console = { log: function() { } };

DirectorJS = {
	req: [],
	userScope: {},
	formats: {},
	userFormats: {},
	previewFormat: {},
	
	initReq: function(salt) {
		var key = salt + Math.floor(Math.random()*1000);
		this.req[key] = new Object;
		this.req[key].params = [];
		return this.req[key];
	},
	
	setHost: function(path, hosted) {
		var hosted = hosted || false;
		this.host = 'http://' + path.replace('http://', '');
		if (!hosted) {
			this.host += '/index.php?';
		}
	},
	
	conn: function(req) {
		if (req) {
			var handler = req.apiMethod + ( req.modified || Math.floor(Math.random()*10000) );
				
			for (var i in this.formats) {
				var f = this.formats[i];
				req.params.push('size[' + i + ']=' + f);
			};
			for (var i in this.userFormats) {
				var f = this.userFormats[i];
				req.params.push('user_size[' + i + ']=' + f);
			};
			if (this.previewFormat) {
				req.params.push('preview=' + this.previewFormat);
			};
			
			req.params = req.params.join('&');
			var qs = req.params;
			var url = this.host + '/api/' + req.apiMethod + '/?'+qs;
			jQuery.ajax({
			    url: url,
			    dataType:"jsonp",
			    jsonpCallback:handler,
			    cache:true,
			    success:function(response){
			    	if (response.error) {
						if (console) {
							console.log(response.error);
						}
					} else {
						req.callback.apply(this, [response.data]);
					}
			    }
			});
		};
	},
	
	App: {
		version: function(callback, options) {
			var req = DirectorJS.initReq('app.version.');
			options = options || {};
			req.apiMethod = 'app_version';
			req.callback = callback;
			DirectorJS.conn(req);
		},
		
		totals: function(callback, options) {
			var req = DirectorJS.initReq('app.totals.');
			options = options || {};
			req.apiMethod = 'app_totals';
			req.callback = callback;
			DirectorJS.conn(req);
		},
		
		limits: function(callback, options) {
			var req = DirectorJS.initReq('app.limits.');
			options = options || {};
			req.apiMethod = 'app_limits';
			req.callback = callback;
			DirectorJS.conn(req);
		}
	},
	
	Format: {
		add: function(options) {
			var defaults = {
				square: 0,
				quality: 85,
				sharpening: 1
			};
			
			for (var i in options) {
				defaults[i] = options[i];
			};
			
			DirectorJS.formats[options.name] = [ defaults.name, defaults.width, defaults.height, defaults.square, defaults.quality, defaults.sharpening ].join(',');
		},
		
		user: function(options) {
			var defaults = {
				square: 0,
				quality: 85,
				sharpening: 1
			};

			for (var i in options) {
				defaults[i] = options[i];
			};

			DirectorJS.userFormats[options.name] = [ defaults.name, defaults.width, defaults.height, defaults.square, defaults.quality, defaults.sharpening ].join(',');
		},
		
		preview: function(options) {
			var defaults = {
				square: 0,
				quality: 85,
				sharpening: 1
			};
			
			for (var i in options) {
				defaults[i] = options[i];
			};
			
			DirectorJS.previewFormat = [ defaults.width, defaults.height, defaults.square, defaults.quality, defaults.sharpening ].join(',');
		},
		
		clear: function() {
			DirectorJS.formats = {};
			DirectorJS.userFormats = {};
			DirectorJS.previewFormat = [];
		}	
	},
	
	Content: {
		get: function(id, callback, options) {
			var req = DirectorJS.initReq('content.get.');
			req.apiMethod = 'get_content';
			req.callback = callback;
			req.params.push("content_id=" + id);
			DirectorJS.conn(req);
		},
		
		list: function(callback, options) {
			var req = DirectorJS.initReq('content.list.');
			
			var defaults = {
				limit: 0,
				only_images: false,
				only_active: true,
				sort_on: 'created_on',
				sort_direction: 'DESC'
			}
			
			DirectorJS.prepAndSendObjs(defaults, options, req);
			
			req.apiMethod = 'get_content_list';
			req.callback = callback;
			DirectorJS.conn(req);
		}
	},
	
	Album: {
		get: function(id, callback, options) {
			var req = DirectorJS.initReq('album.get.');
			
			var defaults = {
				only_active: 1
			};
			DirectorJS.prepAndSendObjs(defaults, options, req);
			
			req.apiMethod = 'get_album';
			req.callback = callback;
			req.params.push("album_id=" + id);
			req.modified = options.modified && '_'+id+'_' + options.modified || null;
			DirectorJS.conn(req);
		},
		
		list: function(callback, options) {
			var req = DirectorJS.initReq('album.list.');
			
			var defaults = {
				only_published: true,
				only_active: true,
				list_only: false,
				only_smart: false,
				exclude_smart: false
			}
			DirectorJS.prepAndSendObjs(defaults, options, req);
			
			req.apiMethod = 'get_album_list';
			req.callback = callback;
			DirectorJS.conn(req);
		},
		
		galleries: function(id, callback, options) {
			var req = DirectorJS.initReq('album.galleries.');
			
			var defaults = {
				exclude: 0
			}
			
			if (undefined !== options && typeof options.exclude == 'object') {
				options.exclude = options.exclude.join(',');
			}
			
			DirectorJS.prepAndSendObjs(defaults, options, req);
			
			req.apiMethod = 'get_associated_galleries';
			req.callback = callback;
			req.params.push("album_id=" + id);
			DirectorJS.conn(req);
		}
	},
	
	Gallery: {
		get: function(id, callback, options) {
			var req = DirectorJS.initReq('gallery.get.');
			
			var defaults = {
				limit: null,
				order: 'display',
				with_content: true
				
			};
			DirectorJS.prepAndSendObjs(defaults, options, req);
			
			req.apiMethod = 'get_gallery';
			req.callback = callback;
			req.params.push("gallery_id=" + id);
			DirectorJS.conn(req);
		}, 
		
		list: function(callback, options) {
			var req = DirectorJS.initReq('gallery.list.');
			req.apiMethod = 'get_gallery_list';
			req.callback = callback;
			DirectorJS.conn(req);
		}
	},
	
	User: {
		scope: function(obj) {
			DirectorJS.userScope = obj;
		},
		
		list: function(callback, options) {
			var req = DirectorJS.initReq('user.all.');
			
			var defaults = {
				sort: 'name'
			}
			DirectorJS.prepAndSendObjs(defaults, options, req);
			
			if (undefined !== DirectorJS.userScope.model) {
				if (undefined == DirectorJS.userScope.all) {
					DirectorJS.userScope.all = false;
				}
				req.params.push('user_scope_model=' + DirectorJS.userScope.model);
				req.params.push('user_scope_id=' + DirectorJS.userScope.id);
				req.params.push('user_scope_all=' + DirectorJS.userScope.all);
			}
			
			req.apiMethod = 'get_users';
			req.callback = callback;
			DirectorJS.conn(req);
		}
	},
	
	prepAndSendObjs: function(obj1, obj2, req) {
		if (typeof obj2 == 'object') {
			if (typeof obj2.tags == 'object') {
				var tmp = {};
				var tags = obj2.tags[0];
				var all;
				if (obj2.tags[1] == 'all') {
					all = 1;
				} else {
					all = 0;
				};
				obj2.tags = tags;
				obj2.tags_exclusive = all;
			};
			
			if (typeof obj2.scope == 'object') {
				var tmp = {};
				var scope = obj2.scope[0];
				var scope_id = obj2.scope[1];
				
				obj2.scope = scope;
				obj2.scope_id = scope_id;
			};
		
			for (var i in obj2) {
				obj1[i] = obj2[i];
			};
		};
		for (var i in obj1) {
			if (i == 'sort_on' && obj1[i] == 'random') {
				req.params.push('buster=' + String(Math.round(Math.random()*10)));
			} ;
			if (String(obj1[i]) == 'true' || String(obj1[i]) == 'false') {
				obj1[i] = Number(obj1[i]);
			};
			req.params.push(i + '=' + obj1[i]);
		};
	}
};


var slideshowpro = {
    	debug:0,
    	win: window.dialogArguments || opener || parent || top,
        kill: false,
        loading: $('#ssp-loading'),
        paged: 20,
        current_page: 1,
        page_start: 0,
        current_album: 0,
        max_width: 600,
        tmp: $("#ssp-insert-table-tmpl"),
        album_container: $("#ssp_album"),
        content_width:0,
        tscale: 1 ,
        no_thumb:'',
        large_lbl : 'Large',
        path:'',
        link_images:1,
        ajax_url:'',
        featured_nonce:'',
        hosted:0,
        image_sizes : {},
        image: {},
        albums: {},

        director_formats: function(o) {
            for (var i in o) {
                DirectorJS.Format.add({
                    'name': i,
                    'width': o[i].w,
                    'height': o[i].h,
                    'crop': o[i].c,
                    'quality': o[i].q,
                    'sharpening': o[i].sh,
                    'square': o[i].sq
                });

            }
            DirectorJS.Format.preview({
                'width': o.large.w,
                'height': o.large.h,
                'crop': o.large.c,
                'quality': o.large.q,
                'sharpening': o.large.sh
            });
        },

        init_director: function() {
            DirectorJS.setHost(this.path, this.hosted );
            this.listAlbums();
            this.director_formats(this.image_sizes);
        },

        insert_init: function(opts) {
        	for( i in opts ){
        		this[i] = opts[i];
        	}
            this.loading.show();
            this.cache.set('content_width', this.content_width );
            this.init_director();

            this.get_album(0);
            $("#ssp_select_sort").bind('change', function() {
                slideshowpro.sort(this);
            });
            $("#ssp_albums").bind('change', function() {
                slideshowpro.change_album(this);
            });

        },

        listAlbums: function() {
            DirectorJS.Album.list(function(data) {
            	
                var total = data.albums.length - 1;
                $.each(data.albums, function(i) {
                	var id = data.albums[i].id;
                	slideshowpro.albums[id] = data.albums[i];
                    try {
                        var mod = slideshowpro.cache.get('album-' + id);
                        if (mod) {
                            mod = JSON.parse(mod);
                            if (mod.modified !== data.albums[i].modified) {
                                slideshowpro.cache.remove('album-' + data.albums[i].id);
                            }
                        }
                    } catch (e) {};
                    var option = new Option(data.albums[i].name, data.albums[i].id);
                    var dropdownList = $("#ssp_albums")[0]
                    dropdownList.add(option, null);
                    if (i == total) {
                        $("#ssp-navigation").show();
                    }
                });
            }, {
                list_only: true
            });

        },

        /** paging function **/
        page_links: function( container, items, per_page, current_page, action ) {
            var pages,tablenav,tablenav_pages,previous,first,link,start_link,this_link,total;
            
            container.empty();
            
            pages = Math.ceil( items.length / per_page ) || 0;
            
            start_link = ( current_page - 3 ) || 1;
            this_link = ( start_link >= 1 ) ? start_link : 1;
            total = ((this_link + 6) < pages ) && this_link + 6 || ( parseInt(this_link + 5) <= pages ) && parseInt(this_link + 5) || pages;
			
			/** create the pages container elemnt **/
			
			tablenav_pages = $('<div></div>').addClass('tablenav-pages');
			
            /** previous button **/
            if ( pages > 1 && current_page !== 1 ) {
                $('<a></a>').addClass('prev page-numbers')
    					    .attr('href', parseInt( current_page - 1 ) )
    					    .html('&laquo;')
    					    .appendTo(tablenav_pages);   
                					            
            } /** conditional first link **/
            if (current_page > 3 && pages > 5) {
                $('<a></a>').addClass('page-numbers')
                			.attr('href','1').text('1')
                			.appendTo(tablenav_pages); 
                			
                $('<span></span>').addClass('page-numbers dots')
                				  .html('&#0133;')
                				  .appendTo(tablenav_pages);  
            }
            
            /** page links **/
            if (pages > 1) {
                for ( var i = this_link; i <= total; i++ ) {
                    if ( i == current_page ) {
                        $('<span></span>').addClass('page-numbers current')
                        				  .html(i)
                        				  .appendTo(tablenav_pages);
                    } else {
                    	$('<a></a>').addClass('page-numbers')
                    				.attr('href', i )
                        		    .html(i)
                        		    .appendTo(tablenav_pages);
                    }
                }
            } /** last link conditional **/
            
            if ( current_page < (pages - 3) && pages > 5 && total != pages ) {
            	$('<span></span>').addClass('page-numbers dots')
                				  .html('&#0133;')
                				  .appendTo(tablenav_pages);
                						  
                $('<a></a>').addClass('page-numbers')
                    		.attr('href', pages )
                            .html(pages)
                        	.appendTo(tablenav_pages);
            }

            /** next button **/
            if ( pages > 1 && current_page !== pages ) {
                $('<a></a>').addClass('next page-numbers')
    					    .attr('href', parseInt(current_page + 1 ) )
    					    .html('&raquo;')
    					    .appendTo(tablenav_pages); 
            } 

            /** set the links **/
            tablenav = $('<div></div>').addClass('tablenav')
            						   .append(tablenav_pages)
            		 .appendTo(container)
            		 .find('a')
                     .bind( 'click', function(e){
                     	e.preventDefault();
                     	return action($(this));
                      });               
        },

        /** load images one by one **/
        load_image: function(container, i, total) {
            var li,preview,span,error,this_image,id,video,img,src;
             
            i = this.intval(i);

            this_image = this.images[i];
            id = this_image.id;

            li = $('<li></li>').attr('id', 'image-'+id).hide();
            span = $('<span></span>');
            preview = $('<div></div>').addClass('tpreview');

           	video = this.intval(this_image.is_video) && $('<div></div>').addClass('ssp-vid-overlay-large').html('&nbsp') || null;

            container.append(li)
            li.append(span)
            span.append(preview)
            preview.append(video);
			if(video && !this_image.thumb){
				this_image.thumb = {url:slideshowpro.no_thumb, width:112, height:112 };
			}
			$(container).queue(function(next){
	            if (this_image.thumb) {
	                src = this_image.thumb.url;
	                $('<img/>').load(function() {
	                    $(this).appendTo(preview);
	                    li.fadeIn('fast');
	                    next();
	                }).error(function(e) {}).attr('src', src)
	                  .css({'cursor': 'pointer'})
	                  .bind('click', function(e) {
	                    slideshowpro.image = this_image;
	                    slideshowpro.director_insert_dialog();
	                });
	            }
            });
        },

        /** loads an album of images **/
        get_album: function(id) {
            this.loading.show();
            this.current_page = 1;
            this.page_start = 0;
            this.images = {};
            this.image = {};
			this.log_message({'albums':this.albums});
            try {
                if (slideshowpro.cache.get('tscale') != slideshowpro.tscale) 
                	slideshowpro.cache.remove('album-' + id);
                	slideshowpro.cache.set('tscale',slideshowpro.tscale);
            } catch (e) {}

            try {
                if (slideshowpro.cache.get('content_width') != slideshowpro.content_width) 
                	slideshowpro.cache.remove('album-' + id);
            } catch (e) {}

            try {
                if (slideshowpro.cache.get('album-' + id) && id != 0) {
                    var data = slideshowpro.cache.get('album-' + id);
                    var tmp = JSON.parse(data);
                    slideshowpro.images = tmp.contents;
                    return slideshowpro.after_load();


                }
            } catch (e) {}

            /** 40 most recent images **/
            if (id == 0) {
                DirectorJS.Content.list(function(data) {
                    slideshowpro.current_album = 'current';
                    slideshowpro.images = data.contents;
                    return slideshowpro.after_load();
                }, {
                    include_metadata: false,
                    limit: '40'
                });
            } else {
                DirectorJS.Album.get(id, function(data) {
                	slideshowpro.log_message(data);
                    slideshowpro.images = data.contents;
                    try {
                        slideshowpro.cache.set('album-' + id, JSON.stringify(data));
                    } catch (e) {
                        slideshowpro.cache.remove('album-' + id)
                    }
                    return slideshowpro.after_load();
                }, {
                    include_metadata: false, modified:slideshowpro.albums[id].modified || null
                });
            }
        },

        /** triggered after an album loads **/
        after_load: function() {
            var s = $("#ssp_select_sort").val();
            var f = function(a) {
                return a.toUpperCase();
            }
            var p = (s == 'title') ? this.sort_by_name : this.sort_by(s, false, parseInt);
            this.images.sort(p);
            this.page_start = 0;
            this.page_end = this.paged;
             
             if(this.images.length){
                this.load_fresh_images();
             }else{
             	$('<p></p>').addClass('ssp-error').text('There are no images in this album.').appendTo(this.album_container);
             }
        },
        
         /** clear images and links **/
        load_fresh_images: function() {
            var ul = null, image_container = null;
            var id = 'album_images-' + this.current_album + '-' + this.page_end;
      		this.log_message(this.images);
      		album_container.html('<ul id="'+id+'"></ul>');
        	this.loading.hide();
        	for( i = this.page_start; i < this.page_end; i++ ){
        		if(this.images[i]){	
        			this.load_image(image_container, i, this.page_end );
        		}
        	}
        	
            this.page_links( $('#ssp-pages'), this.images, this.paged, this.current_page, function(link){
            		$('#'+id ).remove();
                    slideshowpro.current_page = parseInt( link.attr('href') );
                    slideshowpro.page_start = (slideshowpro.current_page - 1) * slideshowpro.paged;
                    slideshowpro.page_end = slideshowpro.page_start + slideshowpro.paged;
                    slideshowpro.load_fresh_images();
                 });

        },



        /** opens the insert dialog **/
        director_insert_dialog: function() {

            /** video or image **/
            var img = this.image;
            var dims = this.get_dimensions();
            if (this.intval(img.is_video)) {
                img.is_video = true;
                img.media_type = 'video';
                img.link = null;
            } else {
                img.is_video = false;
                img.media_type = 'image';
                img.link = img.link || ( this.intval(this.link_images) && img.original.url) || '';

                /** set dimensions **/
                var x = img.original.width;
                var y = img.original.height;

                img.w = img.ow = x;
                img.h = img.oh = y;
                img.xy_ratio = x / y;
                img.constrain = true;
            }

            img.show_featured = this.check_post_thumbnail(img);

            /** load thumb image **/
            var my_image = $('<img/>').load(function() {
                slideshowpro.tmp.tmpl(slideshowpro).appendTo("#ssp-insert-form");
                slideshowpro.prep_insert_form(this);

            }).error(function(e) {}).attr('src', img.thumb.url);
        },


        prep_insert_form: function(image) { /** insert and continue action **/
            $('#ssp-insert-continue').bind('click', function() {
                slideshowpro.action = 'continue';
                slideshowpro.prep_image();
            }); /** insert action **/
            $('#ssp-insert').bind('click', function() {
                slideshowpro.action = 'insert';
                slideshowpro.prep_image();
            }); /** close / cancel action **/
            $('#ssp-close, #ssp-cancel').bind('click', function() {
                slideshowpro.close_insert();
                return false;
            }); /** featured action **/
            $('#ssp-featured').bind('click', function() {
            
                slideshowpro.set_featured(slideshowpro.image.original.url, slideshowpro.image.src, this);
                
            }); /** constrain action **/
            $('#ssp-image-size-custom-width').bind('keyup', function(e) {
                slideshowpro.scale_changed(1);
            }).bind('blur', function(e) {
                slideshowpro.scale_changed(1);
            }).bind('focus', function(e) {
                $('input[name="image[size]"]')[4].checked = true;
            });
            $('#ssp-image-size-custom-height').bind('keyup', function(e) {
                slideshowpro.scale_changed(0);
            }).bind('blur', function(e) {
                slideshowpro.scale_changed(0);
            }).bind('focus', function(e) {
                $('input[name="image[size]"]')[4].checked = true;
            });
            $('#ssp-constrain-img').bind('change', function(e) {
                slideshowpro.image.constrain = $(this).is(':checked');
                slideshowpro.scale_changed(1);
            });

            $('#ssp-thumb-image').append(image);

            /** hide the spinner and show the form **/
            $('#ssp-insert-form').fadeIn('fast');
        },

        get_dimensions: function() {
            var w = 0,
                h = 0,
                c = this.content_width,
                dw = 0,
                dh = 0,
                dims = '';
            this.image_sizes.original = {
                w: this.image.original.width,
                h: this.image.original.height
            };
            for (size in this.image_sizes) {
                w = this.image[size] && this.image[size].width || this.image_sizes[size].w;
                h = this.image[size] && this.image[size].height || this.image_sizes[size].h;
                dw = Math.min(Math.round(w), this.image.original.width);
                dh = Math.min(Math.round(h), this.image.original.height);
                this.image.width = c && c || 600;
                if(this.image[size]){
                	this.image[size].dimensions = dw + ' x ' + dh;
                }else{
                	this.image[size] = {width:w,height:h,url:'',dimensions:dw + ' x ' + dh};
                	}
            }
        },

        //altered function borrowed from image-edit.js in wp-admin/js
        intval: function(f) {
            return f | 0;
        },

        scale_changed: function(x) {
            var w = $('#ssp-image-size-custom-width'),
                h = $('#ssp-image-size-custom-height'),
                warn = $('#ssp-scale-warn'),
                w1 = '',
                h1 = '';
            if (x) {
                h1 = (w.val() != '') ? this.intval(w.val() / this.image.xy_ratio) : '';
                if (this.image.constrain) h.val(h1);
            } else {
                w1 = (h.val() != '') ? this.intval(h.val() * this.image.xy_ratio) : '';
                if (this.image.constrain) w.val(w1);
            }
            if ((h1 && h1 > this.image.oh) || (w1 && w1 > this.image.ow)) warn.css('display', 'block');
            else warn.css('display', 'none');
        },

        /** determine images sort order **/
        sort_by: function(field, reverse, primer) {
            reverse = (reverse) ? -1 : 1;
            return function(a, b) {
                a = a[field];
                b = b[field];
                if (typeof(primer) != 'undefined') {
                    a = primer(a);
                    b = primer(b);
                }
                if (a < b) return reverse * -1;
                if (a > b) return reverse * 1;

                return 0;
            }
        },

        /** change sort order **/
        sort_by_name: function(a, b) {
            var x = a.src.toLowerCase();
            var y = b.src.toLowerCase();
            return ((x < y) ? -1 : ((x > y) ? 1 : 0));
        },

        /** Insert Functions **/

        check_post_thumbnail: function(i) {
        	
            var s = this.image_sizes,h, w,h1,w1,result, max = 0;
            if( !i.post_thumbnail || this.intval(i.is_video) )
            	return false;
            
            if( this.intval(i.original.height) > this.intval(i.original.width) ){
            	h = Math.max(this.intval(s.post_thumbnail.h), this.intval(s.post_thumbnail.w)) / i.xy_ratio;

                if( h <= i.original.height ){
                     return true;
            	}
            }
			w = Math.max(this.intval(s.post_thumbnail.h), this.intval(s.post_thumbnail.w)) * i.xy_ratio;
            if( this.intval(i.original.width) >= this.intval(w) )
            	return true;
            	
            return false;
        },

        set_featured: function(url, src, button) {
            $(button).hide().text('Downloading image...').fadeIn();
            
            var data = {
                action: 'download_and_save_remote_image',
                url: url,
                post_id: post_id || 0,
                src : src
            }
            $.post(this.ajax_url, data, function(response) {
                if (response && !isNaN(response)) {
                    $(button).attr('id', 'wp-post-thumbnail-' + response);
                    WPSetAsThumbnail( response, slideshowpro.featured_nonce );
                }
            });
        },

        /** close insert form dialog **/
        close_insert: function() {
            $('#ssp-insert-form').fadeOut();
            $('#ssp-insert-table').remove();
            return false;
        },

        /** insert and continue **/
        insert_continue: function(html) {
            if (!this.win) this.win = top;
            tinyMCE = this.win.tinyMCE;
            var edCanvas = this.win.document.getElementById('content');
            if (typeof tinyMCE != 'undefined' && (ed = tinyMCE.activeEditor) && !ed.isHidden()) {
                ed.focus();
                if (tinyMCE.isIE) ed.selection.moveToBookmark(tinyMCE.EditorManager.activeEditor.windowManager.bookmark);

                ed.execCommand('mceInsertContent', false, html);
            } else if (typeof edInsertContent == 'function') {
                edInsertContent(edCanvas, html);
            } else {
                $(edCanvas).val($(edCanvas).val() + html);
            }
        },

        /** stage one of insert **/
        prep_image: function() {
            if (this.intval(this.image.is_video)) {
                var ou = this.image.original.url;
                var pv = this.image.lg_preview && this.image.lg_preview.original.url || false;
                var controls = $('#ssp-video-options-controls').is(':checked') ? 'true' : 'false';
                var autostart = $('#ssp-video-options-auto').is(':checked') ? 'true' : 'false';
                var w = Math.round($('#ssp-video-size-custom-width').val() || this.content_width || 600);

                html = '[slideshowpro type="video" url="' + ou + '" preview="' + pv + '"  width="' + w + '" controls="' + controls + '" autostart="' + autostart + '" ]';

                if (this.action == 'insert') return this.win.send_to_editor(html);

                if (this.action == 'continue') return this.insert_continue(html);
            }

            var w = $('#ssp-image-size-custom-width').val();
            var h = $('#ssp-image-size-custom-height').val();
            var c = ($('#ssp-image-size-custom').is(':checked') && w && h) || false;
            if (c) {
                this.image_sizes.custom = {
                    w: w,
                    h: h,
                    c: 1,
                    q: 85,
                    sh: 1,
                    sq: 1
                };
                DirectorJS.Format.clear();
                this.init_director();

                DirectorJS.Content.get(this.image.id, function(data) {
                    slideshowpro.image = data;
                    return slideshowpro.send_to_editor();
                }, {
                    include_metadata: false
                });
            } else {
                return this.send_to_editor();
            }
        },

        /** produces html and passes to the editor **/
        send_to_editor: function() {
            var l = $('#ssp-image-link').val();
            var sr = this.image.src;
            var id = this.image.id;
            var s = $('#ssp-insert-row-size-image').find('input:checked').val();
            var h = Math.round(this.image[s].height);
            var w = Math.round(this.image[s].width);
            var u = this.image[s].url;
            var cl = $('#ssp-insert-row-align').find('input:checked').val();
            var ti = $('#ssp-image-title').val();
            var ca = $('#ssp-image-caption').val();
            var t = ($('#ssp-image-display-title').is(':checked') && ti) || false;
            var c = ($('#ssp-image-display-caption').is(':checked') && ca) || false;
            var o = ((t && c) && t + ' - ' + c) || (t && t) || (c && c) || false;

            /** image **/
            html = '<img src="' + u + '" alt="' + (ca || ti || sr) + '" title="' + (ti || sr) + '" width="' + w + '" height="' + h + '" class="' + cl + ' size-' + s + '" />';

            /** link **/
            html = (l && '<a href="' + (l || u) + '" title="' + (t || sr) + '">' + html + '</a>') || html;

            /** caption **/
            html = o && '[caption id="image_' + id + '" align="' + cl + ' size-' + s + '" width="' + w + '" caption="' + o + '"]' + html + '[/caption]' || html;

            switch (this.action) {
            case 'continue':
                this.insert_continue(html);
                this.close_insert();
                break;

            case 'insert':
                return this.win.send_to_editor(html);
                break;
            }
        },
        sort: function(e) {
            this.album_container.find('li').remove();
            this.current_page = 1;
            this.page_start = 0;
            this.page_end = this.paged;
            var s = $(e).val();
            var f = function(a) {
                return a.toUpperCase();
            }
            var p = (s == 'title') ? this.sort_by_name : this.sort_by(s, false, parseInt);
            this.images.sort(p);
            this.load_fresh_images();
        },

        /** load the selected album **/
        change_album: function(e) {
        	this.album_container.find('ul').remove();
            this.current_album = $(e).val();
            this.get_album(this.current_album);
        },
        
        log_message:function(message){
        	if ( (console) && this.debug ){
				console.log(message);
			}
        },
        
        cache:{ set:function(name, data){
        			try {
                		localStorage.setItem(name, data);
                		return true;
            		} catch (e) { slideshowpro.log_message('error: ' + e ); }
        	  	},
        	  	
        	  	get:function(name){
        	  		 try {
                         var data = localStorage.getItem(name);
                         return data;
                     } catch (e) { slideshowpro.log_message('error: ' + e ); };
        	  	},
        	  	
        	  	remove:function(name){
        	  		try {
                         localStorage.removeItem(name);
                         return true;
                     } catch (e) { slideshowpro.log_message('error: ' + e ); };
        	  	}
        	  }
    }
