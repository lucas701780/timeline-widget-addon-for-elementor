<#
var data = settings.cps_icon_list;

 if(settings.cps_process_layout == 'Horizontal'){
    var preset = settings.cps_preset_hr_style;  
    var pswfe_steps_layout = 'pswfe-steps';
 }
 else{
    var preset = settings.cps_preset_vertical_style;
    var pswfe_steps_layout = 'pswfe-vertical-steps';
 }
 #>
 <ul class="{{pswfe_steps_layout}}  pswfe-process {{preset}}">
 <#

    if(layout == 'Horizontal'){
        var has_arrow = (settings.cps_enable_connector == 'cps-connector-arrow') ? 'pswfe-has-arrow' : "";
        var has_gap = (settings.cps_show_gap === 'yes') ? 'pswfe-has-gap' : "";
        var icon_badge = (settings.cps_selected_badge) ? settings.cps_selected_badge : '1';
        
    }else{
        var has_arrow = (settings.cps_enable_connector == 'cps-connector-arrow') ? 'pswfe-vertical-has-arrow' : "";
        var has_gap = (settings.cps_show_gap === 'yes') ? 'pswfe-vertical-has-gap' : "";
        var icon_badge = (settings.cps_selected_badge) ? settings.cps_selected_badge : '1';
    }
    
#>

 <#   _.each(data, function(item, index){

    var icon_type = (item.cps_selected_icon)?(item.cps_selected_icon):'icon';
    var title_link = (item.cps_website_link.url !="" && item.cps_enable_link=='yes') ?'<a href="'+item.cps_website_link.url+'">'+item.cps_title+'</a>' :'<div '+view.getRenderAttributeString(title_key)+'>'+item.cps_title+'</div>';
    var title_key = view.getRepeaterSettingKey( 'cps_title', 'cps_icon_list',index );
    description_key = view.getRepeaterSettingKey( 'cps_description', 'cps_icon_list',index );



    //inline attributes
      view.addInlineEditingAttributes( title_key, 'none' );
      view.addInlineEditingAttributes( description_key, 'advanced' );

    if(layout == 'Horizontal'){
        var psefe_title_var = ['pswfe-title'];
        var pswfe_description_var = ['pswfe-content-desc'];
        var pswfe_step_segment = 'pswfe-steps-segment';
        var pswfe_badge = 'pswfe-badge';
        var pswfe_step_marker = 'pswfe-steps-marker';
        var pswfe_step_marker_text = 'pswfe-marker-text';
        var pswfe_step_marker_image = 'pswfe-marker-image';
        var pswfe_step_content = 'pswfe-steps-content';

    }
    else{
        var psefe_title_var = ['pswfe-vertical-title'];
        var pswfe_description_var = ['pswfe-vertical-content-desc'];
        var pswfe_step_segment = 'pswfe-vertical-steps-segment';
        var pswfe_badge = 'pswfe-vertical-badge';
        var pswfe_step_marker = 'pswfe-vertical-steps-marker';
        var pswfe_step_marker_text = 'pswfe-vertical-marker-text';
        var pswfe_step_marker_image = 'pswfe-vertical-marker-image';
        var pswfe_step_content = 'pswfe-vertical-steps-content';

    }

       //class for title and description

      view.addRenderAttribute(title_key, {'class' : psefe_title_var,});
      view.addRenderAttribute(description_key, {'class': pswfe_description_var,});

#>
<li class="{{pswfe_step_segment}} pswfe-animation elementor-repeater-item-{{item._id}} {{has_arrow}} {{has_gap}}">
<div class="{{pswfe_step_marker}}">
    <#
    if(icon_type == 'icon'){ #>
      <# var iconHTML = elementor.helpers.renderIcon( view, item.cps_story_icon, { 'aria-hidden': true }, 'i' , 'object' ); #>
      <# if(iconHTML && iconHTML.rendered){#>
        <span class="{{pswfe_step_marker_text}}">
          {{{ iconHTML.value }}}
        </span>
      <#
      } else{
        #>
        <span class="{{pswfe_step_marker_text}}">
        <i class="{{  item.cps_story_icon.value }}" aria-hidden="true"></i>
      </span>
        <#
      }
     }
       else if(icon_type == 'image'){
    #>
    <img src="{{item.cps_icon_image.url}}" class="{{pswfe_step_marker_image}}">
    <#
   }

      else{
        if(icon_type == 'customtext'){ #>
          <span class="{{pswfe_step_marker_text}}">{{{item.cps_icon_text}}}</span>
       <#  }
    }
    #>

    <# if(icon_badge == 'badge-customtext'){
       if(item.cps_badge!=""){
        var steps = (item.cps_badge.length > "2") ? "steps" : "";
      #>

     <span class="{{pswfe_badge}} {{steps}} {{settings.cps_badge_position}}">{{{ item.cps_badge }}}</span>

    <#
       }
  }
    #>
    <div class="pswfe-hover-animation {{settings.cps_hover_animation}}"></div>
    </div>
<#
if(item.cps_title !="" || item.cps_description !=""){
  #>
    <div class="{{pswfe_step_content}}">
            <div {{{view.getRenderAttributeString( title_key )}}}>
              <# if(item.cps_website_link.url !="" && item.cps_enable_link=='yes'){ #>
                <a href="{{item.cps_website_link.url}}">{{{item.cps_title}}}</a>
              <# }else{ #>
                <div {{{view.getRenderAttributeString(title_key)}}}>{{{item.cps_title}}}</div>
              <# } #>
            </div>
            <div {{{view.getRenderAttributeString( description_key )}}}>{{{item.cps_description}}}</div>

        </div>
        <# } #>
        </li>
 <# }); #>
    </ul>