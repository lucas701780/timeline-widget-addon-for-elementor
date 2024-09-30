<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<#
 
var widgetId=view.getID();
var sidesToShow ='';
var multiColor=1;
var cutomStyle='';
var styleTag='style';
var navigation_hr_position =  settings.twae_hr_navigation_position; 
if(settings.twae_slides_to_show.size!==undefined){
	 sidesToShow = settings.twae_slides_to_show.size;
}else{
	sidesToShow =settings.twae_slides_to_show !==undefined?settings.twae_slides_to_show:2;
}

var hightlighted_showslides =settings.twae_highlighted_to_show !==undefined?settings.twae_highlighted_to_show:3; 

var label_ht_show ='' ;
if(settings.twae_layout == "horizontal-highlighted"){
	label_ht_show='yes';
}

var thumb_content = settings.twae_content_side_by_side;
var thumb ='' ;
if(thumb_content == 'yes'){
 thumb = 'thumb';
}		

var label_content_top = '';
var label_content_inside = settings.twae_label_inside != 'no' && settings.twae_layout != "horizontal-highlighted" ? settings.twae_label_inside : '';
var label_content_main_cls = label_content_inside != '' ? 'label_content_top' : '';
var image_lightbox = settings.twae_lightbox_settings !== undefined && settings.twae_content_in_popup != 'yes' ? settings.twae_lightbox_settings : ''; 
var autoplay_stop_hover = settings.twae_autoplaystop_mousehover !== undefined ? settings.twae_autoplaystop_mousehover : ''; 
var image_hover_effect = settings.twae_image_hover_effect == 'yes' && settings.twae_content_in_popup != 'yes' ? ' twae-img-effect' : '';
var auto_height          = settings.twae_slides_height === 'default-height' ? 'true' : '';
var spaceBW = settings.twae_h_space_bw.size;				
var sidesHeight = settings.twae_slides_height;
var autoplay = settings.twae_autoplay;
var infiniteLoop=settings.twae_infinite_loop;
var speed=settings.twae_speed;
var line_filling=settings.center_line_filling!="undefined" && settings.center_line_filling=="yes" ? "true" : "";

var twae_bg_type='';
if(settings.twae_cbox_background_type!=='undefined' && settings.twae_cbox_background_type=='multicolor'){
	twae_bg_type='twae-bg-multicolor';
}else if(settings.twae_cbox_background_type!=='undefined' && settings.twae_cbox_background_type=='gradient'){
	twae_bg_type='twae-bg-gradient';
}else{
	twae_bg_type='twae-bg-simple';
}

var twae_display_icons = settings.twae_display_icons;
var icon_empty='';
if(twae_display_icons == "displayicons"){
	icon_empty = 'twae-story-icon';
}
if(twae_display_icons == "displaydots"){
	icon_empty = 'twae-story-no-icon';
}
if(twae_display_icons == "displaynone"){
	icon_empty = 'twae-story-no-dot';
}
var icon_empty_default = icon_empty;

var enablePopup='';
	if(settings.twae_content_in_popup!=="undefined" &&
	settings.twae_content_in_popup=="yes"
	){
		enablePopup="yes";
	}else{
	  enablePopup='no';
	}

var twae_cbox_connector_style='';
if(settings.twae_cbox_connector_style!=='undefined' && (settings.twae_cbox_connector_style=="default" || settings.twae_cbox_connector_style=="")){
	if(timeline_style=="style-2" || timeline_style=="style-4"){
		twae_cbox_connector_style='twae-arrow-line';
	}else{
		twae_cbox_connector_style='twae-arrow';
	}
}else{
	twae_cbox_connector_style=settings.twae_cbox_connector_style?settings.twae_cbox_connector_style:'twae-arrow';
}

twae_label_enable=settings.twae_label_background?settings.twae_label_background:'';
twae_label_connector=settings.twae_label_connector_style?settings.twae_label_connector_style:'';
var twae_label_bg ='';
var twae_label_bg_class ='';
if(twae_label_enable == 'yes'){
	twae_label_bg_class="twae-label-bg ";
}
if(twae_label_enable == 'yes' && twae_label_connector == 'default'){
	twae_label_bg ="twae-lbl-arrow";
}else if(twae_label_enable == 'yes' && twae_label_connector == 'twae-arrow-line'){
	twae_label_bg ="twae-lbl-arrow-line";
}


if(settings.twae_layout != 'compact'){

if(enable_navigation == 'yes'){
	#>
	<div class="twae-hor-nav-wrapper"><div id="twae-horizontal-navigationBar-{{widgetId}}" class="twae-horizontal-navigationBar twae-horizontal-navigation-{{navigation_hr_position}}">

</div><div class="swiper-button-next twae-nav-next"> <i class="fas fa-chevron-right"></i></div>
	 <div class="swiper-button-prev twae-nav-prev"> <i class="fas fa-chevron-left"></i></div>
	</div>
<# }
}
#>

<div  id="twae-wrapper-{{widgetId}}" data-style="{{timeline_style}}" data-enable-popup="{{enablePopup}}" class="twae-wrapper twae-horizontal-timeline {{timeline_layout_wrapper}} {{timeline_style}} {{twae_bg_type}} {{label_content_main_cls}} {{twae_line_filler}} {{twae_bg_hover}} {{label_content_inside}}">
<div class="twae-wrapper-inside">
	<#
	var twae_bg_hover='';
	if(settings.twae_cbox_background_type_hover!=='undefined' && settings.twae_cbox_background_type_hover=='simple'){
		twae_bg_hover='twae-bg-hover';
	}
	if(settings.twae_layout == 'horizontal-highlighted'){ #>
		<div class="twae-year-slider-section">
			<div class="year-swiper-container swiper-container" data-slidestoshow="{{hightlighted_showslides}}"  data-spacebw="{{spaceBW}}">
				<div class="twae-slider-wrapper swiper-wrapper">
		<#  _.each( settings.twae_list, function( item, index ) {
		
		var twae_icon_type = item.twae_icon_type;
		var twae_story_icon = item.twae_story_icon.value;
		var twae_icon_text = item.twae_icon_text;
		var twae_display_icon = item.twae_display_icon;
		var icon_cls = '';
		if((twae_icon_type=="" && twae_story_icon!="far fa-clock" && twae_story_icon!="") || twae_icon_type=="icon" || twae_icon_type=="image" || twae_icon_type=="customtext"){
			icon_empty = 'twae-story-icon';
		}
		else if(twae_icon_type=="none"){
			icon_empty = 'twae-story-no-dot';
		}
		else if((twae_display_icon=="" && twae_icon_type!="icon" && twae_icon_type!="image" && twae_icon_type!="customtext" && twae_icon_type!="none") || twae_icon_type=="dot"){
			icon_empty = 'twae-story-no-icon';
		}
		else{
			icon_empty = icon_empty_default; 
		}

		date_label_key = view.getRepeaterSettingKey( 'twae_date_label', 'twae_list',index ),
		sub_label_key = view.getRepeaterSettingKey( 'twae_extra_label', 'twae_list',index ),

		view.addRenderAttribute( date_label_key, {'class':  'twae-label-big'} );
		view.addRenderAttribute( sub_label_key, {'class':  'twae-label-small'} );

		var twaeiconHTML = elementor.helpers.renderIcon( view, item.twae_story_icon, { 'aria-hidden': true }, 'i' , 'object' );

		twae_label_enable=settings.twae_label_background?settings.twae_label_background:'';

		var twae_label_bg_class ='';
		if(twae_label_enable == 'yes'){
			twae_label_bg_class="twae-label-bg ";
		}
		if(twae_label_enable == 'yes' && twae_label_connector == 'default'){
			twae_label_bg ="twae-lbl-arrow";
		}else if(twae_label_enable == 'yes' && twae_label_connector == 'twae-arrow-line'){
			twae_label_bg ="twae-lbl-arrow-line";
		} #>

		<div class="twae-story swiper-slide elementor-repeater-item-{{ item._id }} {{twae_bg_hover}} {{icon_empty}}">
			<div class="twae-labels {{ twae_label_bg_class }} {{ twae_label_bg }}">
				<#
					if(twae_label_enable == 'yes'){
				#>
					<div class="twae-inner-label">
				<# 
					}
				#>
						<div {{{ view.getRenderAttributeString( date_label_key ) }}} >{{{ item.twae_date_label }}}</div>
				<#
					if( item.twae_extra_label != ''){
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
			if(twae_icon_type=="none") { }
			else if((twae_display_icon=="" && twae_icon_type!="icon" && twae_icon_type!="image" && twae_icon_type!="customtext" && twae_icon_type!="none") || twae_icon_type=="dot")
			{ #>
				<div class="twae-icondot"></div>
			<# }
			else if((twae_icon_type=="" && twae_story_icon!="far fa-clock" && twae_story_icon!="") || twae_icon_type=="icon" || twae_icon_type=="image" || twae_icon_type=="customtext")
			{ #>
				<div class="twae-icon {{icon_cls}}">
						<#
						if( twae_icon_type == 'image' ){
						#>
						<img src="{{ item.twae_icon_image.url }}">
						<#
						}
						else if( twae_icon_type == 'customtext' ){  
							#>                 
							<span class="twae_icon_text">{{{ twae_icon_text }}}</span>
							<#
						}else{
							if ( twaeiconHTML && twaeiconHTML.rendered ) {
								#>
								{{{ twaeiconHTML.value }}}
								<#
							} else { #>
								<i class="{{ item.twae_story_icon.value }}" aria-hidden="true"></i>
							<# }
						} #>
				</div>  
			<# }
			else if(twae_display_icons == "displaynone") { }
			else if(twae_display_icons == "displaydots")
			{ #>
				<div class="twae-icondot"></div>
			<# }
			else
			{ #>
				<div class="twae-icon">
					<# 
					twaeiconHTML = elementor.helpers.renderIcon( view, settings.twae_story_icons, { 'aria-hidden': true }, 'i' , 'object' );
					if ( twaeiconHTML && twaeiconHTML.rendered ) { #>
						{{{ twaeiconHTML.value }}}
					<# } else { #>
						<i class="{{ settings.twae_story_icons.value }}" aria-hidden="true"></i>
					<# } #>  
				</div> 
			<# } #>
		</div>
		<#
		countItem = countItem+1;
		}); #>
				</div>
			</div>
		</div>
	<#
	}
	#>
				
	<div class="twae-slider-container swiper-container {{thumb}}" 
	dir="<?php echo esc_attr( $dir ); ?>" data-slidestoshow="{{sidesToShow}}" data-spacebw="{{spaceBW}}" data-autoplay="{{autoplay}}" data-speed="{{speed}}" data-infinite-loop="{{infiniteLoop}}" data-style="{{timeline_style}}" data-stop-autoplay-onhover="{{autoplay_stop_hover}}" data-auto-height="{{auto_height}}" data-line-filling="{{line_filling}}">

	<div class="twae-slider-wrapper swiper-wrapper {{sidesHeight}}">
	<#
	if(settings.navigation_control_icon!=="undefined"){
		var navi_icon_cls=settings.navigation_control_icon;
		var navi_left_icon= navi_icon_cls;
		var navi_right_icon=navi_icon_cls.replace("left", "right");
	}else{
		var navi_left_icon='fas fa-chevron-left';
		var navi_right_icon='fas fa-chevron-right';
	}

var twae_line_filler='';
if(settings.center_line_filling!=='undefined' && settings.center_line_filling=='yes'){
	twae_line_filler='twae-line-filler';
}

_.each( settings.twae_list, function( item, index ) {
			var timeline_image = {
				id: item.twae_image.id,
				url: item.twae_image.url,
				size: item.twae_thumbnail_size,
				dimension: item.twae_thumbnail_custom_dimension,
				model: view.getEditModel()
			};
			var image_url = elementor.imagesManager.getImageUrl( timeline_image );
			var video_url = item.twae_video_url;

			var twae_icon_type = item.twae_icon_type;
			var twae_story_icon = item.twae_story_icon.value;
			var twae_icon_text = item.twae_icon_text;
			var twae_display_icon = item.twae_display_icon;
			var icon_cls = '';
			if((twae_icon_type=="" && twae_story_icon!="far fa-clock" && twae_story_icon!="") || twae_icon_type=="icon" || twae_icon_type=="image" || twae_icon_type=="customtext"){
				icon_empty = 'twae-story-icon';
			}
			else if(twae_icon_type=="none"){
				icon_empty = 'twae-story-no-dot';
			}
			else if((twae_display_icon=="" && twae_icon_type!="icon" && twae_icon_type!="image" && twae_icon_type!="customtext" && twae_icon_type!="none") || twae_icon_type=="dot"){
				icon_empty = 'twae-story-no-icon';
			}
			else{
				icon_empty = icon_empty_default; 
			}

			var year_key = view.getRepeaterSettingKey( 'twae_year', 'twae_list',index ),
			date_label_key = view.getRepeaterSettingKey( 'twae_date_label', 'twae_list',index ),
			sub_label_key = view.getRepeaterSettingKey( 'twae_extra_label', 'twae_list',index ),
			title_key = view.getRepeaterSettingKey( 'twae_story_title', 'twae_list',index ),
			description_key = view.getRepeaterSettingKey( 'twae_description', 'twae_list',index );

			view.addRenderAttribute( year_key, {'class':  'twae-year-text'} );
			view.addRenderAttribute( date_label_key, {'class':  'twae-label-big'} );
			view.addRenderAttribute( sub_label_key, {'class':  'twae-label-small'} );
			view.addRenderAttribute( title_key, {'class': 'twae-title'});
			view.addRenderAttribute( description_key, {'class':  'twae-description'} );
		   
			var twaeiconHTML = elementor.helpers.renderIcon( view, item.twae_story_icon, { 'aria-hidden': true }, 'i' , 'object' );
			
			if(item.twae_thumbnail_size == 'medium_large' || item.twae_thumbnail_size == 'large'){
			var image_width = 'full';          
			}   
			else{
			var image_width = 'small'; 
			}
		  #>
	<div id="twae-article-{{ item._id }}" class="twae-story swiper-slide elementor-repeater-item-{{ item._id }} {{icon_empty}}" data-index="{{index}}" data-multicolor="{{multiColor}}">	
			<div class="twae-story-line"></div>
				<#  
				if(settings.twae_layout != 'horizontal-highlighted'){
					if(item.twae_show_year_label == 'yes'){
						#>
						<div class="twae-year">
							<div {{{ view.getRenderAttributeString( year_key ) }}}>{{{ item.twae_year }}}</div>
						</div>
						<#
					};
					if(label_content_inside == ''){  
						if(item.twae_date_label != '' || item.twae_extra_label != ''){
							#>  
						<div class="twae-labels {{ twae_label_bg_class }} {{ twae_label_bg }}">
							<#
								if(twae_label_enable == 'yes'){
							#>
								<div class="twae-inner-label">
							<# 
								}
							#>
									<div {{{ view.getRenderAttributeString( date_label_key ) }}} >{{{ item.twae_date_label }}}</div>
							<#
								if( item.twae_extra_label != ''){
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
					}
					}
					if(twae_icon_type=="none") { }
					else if((twae_display_icon=="" && twae_icon_type!="icon" && twae_icon_type!="image" && twae_icon_type!="customtext" && twae_icon_type!="none") || twae_icon_type=="dot")
					{ #>
						<div class="twae-icondot"></div>
					<# }
					else if((twae_icon_type=="" && twae_story_icon!="far fa-clock" && twae_story_icon!="") || twae_icon_type=="icon" || twae_icon_type=="image" || twae_icon_type=="customtext")
					{ #>
						<div class="twae-icon {{icon_cls}}">
								<#
								if( twae_icon_type == 'image' ){
								#>
								<img src="{{ item.twae_icon_image.url }}">
								<#
								}
								else if( twae_icon_type == 'customtext' ){  
									#>                 
									<span class="twae_icon_text">{{{ twae_icon_text }}}</span>
									<#
								}else{
									if ( twaeiconHTML && twaeiconHTML.rendered ) {
										#>
										{{{ twaeiconHTML.value }}}
										<#
									} else { #>
										<i class="{{ item.twae_story_icon.value }}" aria-hidden="true"></i>
									<# }
								} #>
						</div>  
					<# }
					else if(twae_display_icons == "displaynone") { }
					else if(twae_display_icons == "displaydots")
					{ #>
						<div class="twae-icondot"></div>
					<# }
					else
					{ #>
						<div class="twae-icon">
							<# 
							twaeiconHTML = elementor.helpers.renderIcon( view, settings.twae_story_icons, { 'aria-hidden': true }, 'i' , 'object' );
							if ( twaeiconHTML && twaeiconHTML.rendered ) { #>
								{{{ twaeiconHTML.value }}}
							<# } else { #>
								<i class="{{ settings.twae_story_icons.value }}" aria-hidden="true"></i>
							<# } #>  
						</div> 
					<# } 
				} #>
					
					<div class="{{twae_cbox_connector_style}}"></div>

					<# 
					 if(timeline_style=='style-4' || enablePopup=="yes"){ #>
							<div class="twae-content" data-aos="{{ animation }}">
								<#
								if(label_content_inside != '' && settings.twae_layout != "horizontal-highlighted"){
									if(item.twae_date_label != '' || item.twae_extra_label != ''){ 
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
											if( item.twae_extra_label != ''){
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
								}
								}
								#>
								<div {{{ view.getRenderAttributeString( title_key ) }}}>
								<a href="#twae-popup-{{ item._id }}" class="twae-popup-links">
									{{{ item.twae_story_title}}}                           
								</a>
								</div>    
							</div>
							<div id="twae-popup-{{ item._id }}" class="twae-popup-content elementor-repeater-item-{{ item._id }}" style="display:none;"> 
								 <?php require TWAE_PRO_PATH . 'widgets/story-timeline/editor-layouts/story-content-template.php'; ?>
							</div>
						 <# }else{ #>  
							 <?php require TWAE_PRO_PATH . 'widgets/story-timeline/editor-layouts/story-content-template.php'; ?>
							<# }   #>  
							
						 </div>
<# 

if('twae-bg-multicolor' === twae_bg_type){
	var customBgColor        = item.twae_custom_story_bgcolor;
	var customConnectorColor = item.twae_custom_cbox_connector_bg_color;
	if('' !== customBgColor || '' !== customConnectorColor){
		cutomStyle += '.twae-wrapper#twae-wrapper-' + widgetId + ' #twae-article-' + item._id + '.twae-story{';
		cutomStyle += '' !== customBgColor ? '--tw-cbx-bg' + multiColor + ': ' + customBgColor + ';' : '';
		cutomStyle += '' !== customConnectorColor ? '--tw-arw-bg' + multiColor + ': ' + customConnectorColor + ';' : '';
		cutomStyle += '}';
	}
}

4 === multiColor ? multiColor = 1 : multiColor++;
countItem = countItem+1;
}); #>
	</div></div></div>
		<!-- Add Pagination -->        
	<!--  <div class="twae-pagination"></div>-->
		<!-- Add Arrows -->
		
		<div class="twae-button-prev"><i class="{{navi_left_icon}}"></i></div>
		<div class="twae-button-next "><i class="{{navi_right_icon}}"></i></div>
		<div class="twae-h-line"></div>
		

		<# if ( 'true' === line_filling ) { #>
			<div class="twae-line-fill"></div>
		<# } #>

	</div>
	<# if('' !== cutomStyle){ #>
		<{{styleTag}}>{{{cutomStyle}}}</{{styleTag}}>
	<# } #>

