<#
	var image_highlighted_show = '';
	if(image_url == ''){
		image_highlighted_show = ' twae-hg-image-not';
	}
	if(item.twae_media == 'slideshow'){
		if(item.twae_slideshow == ''){
			image_highlighted_show = ' twae-hg-image-not';
		}else{
			image_highlighted_show = '';
		}
	}
	var containerCls='';
	if(timeline_style=='style-4'){ 
		containerCls='twae-content';
	}else{
		containerCls='twae-data-container twae-content';
	} 
#>
<div class="{{containerCls}} {{image_highlighted_show}}">
	<#
	if((label_content_inside != '' || label_content_top != '') && (enablePopup == "no" && settings.twae_layout != "horizontal-highlighted")){
	#>								  
		<div class="twae-labels {{ twae_label_bg_class }} {{ twae_label_bg }}" data-aos="{{ animation }}">
			<#
				if(twae_label_enable == 'yes'){
			#>
				<div class="twae-inner-label">
			<# 
				}
			#>
					<div {{{ view.getRenderAttributeString( date_label_key ) }}} >{{{ item.twae_date_label }}}</div>
				<#
					if( item.twae_extra_label != '' && label_content_top == ''){
				#>
				<div {{{ view.getRenderAttributeString( sub_label_key ) }}} >{{{ item.twae_extra_label }}}</div>
				<#
					}
					if(twae_label_enable == 'yes'){
				#>
					</div>
				<# 
					}
				#>
		</div>
			<#
		};
	const default_img = "assets/images/placeholder.png";
	 if( item.twae_media == 'image' ){
		if(image_url != ''){
	#>  
		<div class="twae-media {{item.twae_thumbnail_size}}{{image_hover_effect}}">
			<#
			if(enablePopup == 'yes' && image_url!=undefined){
			if (image_url.includes(default_img)==false){
			#>
			<img src="{{ image_url }}" />
			<#
			} 
			}
			else{
				if(image_lightbox == 'yes'){ #>
					<a class="wplightbox" href="{{image_url}}">
				<# }
				#>
					<img src="{{ image_url }}" />
				<#
				if(image_lightbox == 'yes'){ #>
					</a>
				<# };
			}
		#>
		</div>
		<#
		};
	}else if( item.twae_media == 'slideshow'){
		var twae_slideshow_autoplay = item.twae_slideshow_autoplay;
		if(twae_slideshow_autoplay == "true"){
			twae_slideshow_autoplay = "true";
		}
		else{
			twae_slideshow_autoplay = "false"; 
		}
		if(item.twae_slideshow != ''){
			#>
			<div class="twae-media{{image_hover_effect}}">
			<div id="twae-slideshow-{{ item._id }}random_number" class="twae-slideshow swiper-container" dir="<?php echo esc_attr( $dir ); ?>" data-slideshow_autoplay="{{twae_slideshow_autoplay}}">
				<div class="swiper-wrapper">
				<#
				_.each( item.twae_slideshow, function( image ) { 
					if(image_lightbox == 'yes'){ #>
					<a class="swiper-slide" data-elementor-lightbox-slideshow="twae_img_lightbox_{{ item.id }}random_number" href="{{image.url}}">
					<# }
				#>
					<img class="swiper-slide" src="{{ image.url }}">
				<# 
					if(image_lightbox == 'yes'){ #>
						</a>
					<# };
				});
				#>
				</div>                  
				<div class="twae-icon-left-open"></div>
				<div class="twae-icon-right-open"></div>
			</div>
			</div>
			<#
		}
	}else if(item.twae_media == 'video' ){
		if(video_url!= ''){
			var match = video_url.match(/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/);
			var video_id = (match&&match[7].length==11)?match[7]:false;
		
			if(video_id != false){
				#>
				<div class="twae-media"><iframe width="100%" src="https://www.youtube.com/embed/{{ video_id }}" frameborder="0" allowfullscreen></iframe></div>
				<#
			}
			else{
				#>
				<div class="twae-media"><span class="twae-wrong-url">Wrong URL</span></div>
				<#
			}
			
		}
	}
	if(item.twae_story_title != ''){
		 #>
		 <div {{{ view.getRenderAttributeString( title_key ) }}} >{{{ item.twae_story_title}}}</div>
	 <# } #>
	<div {{{ view.getRenderAttributeString( description_key ) }}} >{{{ item.twae_description }}}
	<# if(item.twae_title_link =="yes"){
				var btn_txt=item.twae_button_txt?item.twae_button_txt:'Read more';
			   #>
				<div class="twae-button"><a class="elementor-button" href="{{item.twae_story_link}}">{{{btn_txt}}}</a></div>

		<# } #>
</div></div>
