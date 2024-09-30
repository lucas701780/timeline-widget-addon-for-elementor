<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<#
	var countItem = 1;
	var multiColor=1;
	var widgetId=view.getID();
	var navigation_style =  settings.twae_navigation_style; 
	var navigation_position =  settings.twae_navigation_position; 
	var timeline_layout_wrapper = 'twae-both-sided';
	var cutomStyle='';
	var styleTag='style';
	if(settings.twae_layout == 'one-sided'){
		var timeline_layout_wrapper = 'twae-vertical-right';
	}
	if(settings.twae_layout == 'left-sided'){
		var timeline_layout_wrapper = 'twae-vertical-left';
	}
	var twae_cbox_connector_style='';
	if(settings.twae_cbox_connector_style!=='undefined' && (settings.twae_cbox_connector_style=="default" || settings.twae_cbox_connector_style=="")){
		if(timeline_style=="style-2"){
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

	var label_content_top = settings.twae_label_content_top != 'no' ? settings.twae_label_content_top : '';
	var label_content_inside = settings.twae_label_inside != 'no' ? settings.twae_label_inside : '';
	var image_lightbox = settings.twae_lightbox_settings !== undefined && settings.twae_content_in_popup != 'yes' ? settings.twae_lightbox_settings : ''; 
	var image_hover_effect = settings.twae_image_hover_effect == 'yes' && settings.twae_content_in_popup != 'yes' ? ' twae-img-effect' : '';
	
	var enablePopup='';
	if(settings.twae_content_in_popup!=="undefined" &&
	settings.twae_content_in_popup=="yes"
	){
		enablePopup="yes";
	}else{
		enablePopup='no';
	}
	var image_outside = settings.twae_image_outside_box != 'no' && settings.twae_layout == 'centered' && enablePopup != 'yes' ? settings.twae_image_outside_box : '';

	<!-- image content inside if image outside on -->
	if(label_content_inside != 'twae-label-content-inside' && label_content_top != 'twae-label-content-top' && image_outside == 'twae_image_outside'){
		label_content_inside = "twae-label-content-inside";
	};

   var random_number = Math.floor(Math.random() * 100);  
	var line_filling='';
   if(settings.center_line_filling!="undefined" && settings.center_line_filling=="yes"){
	line_filling="on";
   }

	var twae_bg_hover='';
	if(settings.twae_cbox_background_type_hover!=='undefined' && settings.twae_cbox_background_type_hover=='simple'){
		twae_bg_hover='twae-bg-hover';
	}

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


if(settings.twae_layout != 'compact'){

	if(enable_navigation == 'yes'){
		#>
		<nav class="twae-navigationBar twae-out-viewport twae-navigation-{{navigation_position}} {{navigation_style}}" id="twae-navigationBar-{{widgetId}}">
			<#
			if(navigation_style == 'style-3'){
				#>
				<div class="twae-nav-icon">
					<span></span>
					<span></span>
					<span></span>
				</div> <#  } #>
			</nav>
	<# }
} 

	var container_cls='';
	if(settings.twae_layout == 'compact'){
		var container_cls='twae-compact';
	}
#>

<div id="twae-wrapper-{{widgetId}}" class="twae-vertical twae-wrapper {{ timeline_layout_wrapper }} {{timeline_style}} {{twae_bg_type}}"
data-line-filling="{{line_filling}}" data-enable-popup="{{enablePopup}}" data-style="{{timeline_style}}" data-space="{{space}}">

	<div class="twae-start"></div>  
	<div class="twae-line twae-timeline {{container_cls}}">
		<#
		   
			
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

			if(item.twae_thumbnail_size == 'medium_large' || item.twae_thumbnail_size == 'large'){
			var image_width = 'full';          
			}   
			else{
			var image_width = 'small'; 
			}

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
			else if((twae_display_icon=="" && twae_icon_type!= "icon" && twae_icon_type!="image" && twae_icon_type!="customtext" && twae_icon_type!="none") || twae_icon_type=="dot"){
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

			view.addRenderAttribute( year_key, {'class':  'twae-year-label twae-year-text'} );
			view.addRenderAttribute( date_label_key, {'class':  'twae-label-big'} );
			view.addRenderAttribute( sub_label_key, {'class':  'twae-label-small'} );
			view.addRenderAttribute( title_key, {'class': 'twae-title'});
			view.addRenderAttribute( description_key, {'class':  'twae-description'} );

	   if(settings.twae_layout != 'compact'){

			if(timeline_style!='style-4' || enablePopup!="yes"){
		  
			if(enablePopup!="yes"){
				view.addInlineEditingAttributes( title_key, 'none' );
				view.addInlineEditingAttributes( description_key, 'advanced' );
			}
			view.addInlineEditingAttributes( date_label_key, 'none' );
			view.addInlineEditingAttributes( sub_label_key, 'none' );
		}
	   
			var story_alignment = "twae-story-right";
				if(settings.twae_layout == 'centered' || settings.twae_layout == 'compact'){
					
					if ( countItem % 2 == 0) {
						var story_alignment = "twae-story-left";						
					}
				}
			}
  if(settings.twae_layout != 'compact'){
		 if(item.twae_show_year_label == 'yes'){
				#>
				<div class="twae-year-container twae-year elementor-repeater-item-{{ item._id }} ">
					<div {{{ view.getRenderAttributeString( year_key ) }}} >{{{ item.twae_year }}}</div>
				</div>
				<#
				}
	 }  #>

				<article id="twae-{{ item._id }}" class="twae-story elementor-repeater-item-{{ item._id }} twae-repeater-item {{ story_alignment }} {{icon_empty}} {{twae_bg_hover}} {{label_content_inside}} {{label_content_top}} {{image_outside}}" data-multicolor="{{multiColor}}">
					<# 
						if(label_content_inside == '' && label_content_top == ''){ 
					#>								  
				<div class="twae-labels {{twae_label_bg_class }} {{ twae_label_bg }}" data-aos="{{ animation }}">
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
							if(twae_icon_type=="none") { }
							else if((twae_display_icon=="" && twae_icon_type!="icon" && twae_icon_type!="image" && twae_icon_type!="customtext" && twae_icon_type!="none") || twae_icon_type=="dot")
							{ #>
								<div class="twae-icondot"></div>
							<# }
							else if((twae_icon_type=="" && twae_story_icon!="far fa-clock" && twae_story_icon!="") || twae_icon_type=="icon" || twae_icon_type=="image" || twae_icon_type=="customtext")
							{ #>
								<div class="twae-icon">
									<# if( twae_icon_type == 'image' ){ #>
										<img src="{{ item.twae_icon_image.url }}">
									<# }else if( twae_icon_type == 'customtext' ){ #>                 
										<span class="twae_icon_text">{{{ twae_icon_text }}}</span>
									<# }else{
										twaeiconHTML = elementor.helpers.renderIcon( view, item.twae_story_icon, { 'aria-hidden': true }, 'i' , 'object' );
										if ( twaeiconHTML && twaeiconHTML.rendered ) { #>
											{{{ twaeiconHTML.value }}}
										<# } else { #>
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

						 <div class="{{twae_cbox_connector_style}}"></div>
					
						 <#
						 if(enablePopup=="yes"){
						  #>
							<div class="twae-content" data-aos="{{ animation }}">
								<# 
								if(label_content_inside != '' || label_content_top != '' && enablePopup=="yes"){ 
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
							<div>
						 <# }else{ #>  
							 <?php require TWAE_PRO_PATH . 'widgets/story-timeline/editor-layouts/story-content-template.php'; ?>
							<# }   #>  
						
							
						 </article>            
			   
 <#  
if('twae-bg-multicolor' === twae_bg_type){
	var customBgColor        = item.twae_custom_story_bgcolor;
	var customConnectorColor = item.twae_custom_cbox_connector_bg_color;
	if('' !== customBgColor || '' !== customConnectorColor){
		cutomStyle += '.twae-wrapper#twae-wrapper-' + widgetId + ' #twae-' + item._id + '.twae-story{';
		cutomStyle += '' !== customBgColor ? '--tw-cbx-bg' + multiColor + ': ' + customBgColor + ';' : '';
		cutomStyle += '' !== customConnectorColor ? '--tw-arw-bg' + multiColor + ': ' + customConnectorColor + ';' : '';
		cutomStyle += '}';
	}
}
4 === multiColor ? multiColor = 1 : multiColor++;
countItem = countItem+1;
});
if(settings.center_line_filling!== "undefined" && settings.center_line_filling=="yes"){
	#>
	<div class="twae-inner-line"></div>
	<#   
}
 #>

		</div>
		<div class="twae-end"></div>   
	</div>
	<# if('' !== cutomStyle){ #>
		<{{{styleTag}}}>{{{cutomStyle}}}</{{{styleTag}}}>
	<# } #>

