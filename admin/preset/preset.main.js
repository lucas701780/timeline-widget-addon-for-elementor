( function( $ ) {
    jQuery( window ).on( 'elementor:init', function() {
       
        var preset_data = elementor.modules.controls.BaseData.extend({
            isTwaePreset: function() {   
                var checklayout = jQuery('.elementor-control.elementor-control-twae_preset_vertical_style.elementor-control-type-twae_preset_style.elementor-label-inline.elementor-control-separator-default.elementor-hidden-control select').data('setting');
                if(checklayout!=undefined){
                    return "twae_preset_hr_style" === this.model.get( "name" ) && -1 !== this.getWidgetName().indexOf( "timeline-" );
                }else{
                    return "twae_preset_vertical_style" === this.model.get( "name" ) && -1 !== this.getWidgetName().indexOf( "timeline-" );
                }
            },
            onReady: function() {

                window.twaePresets = window.twaePresets || {};
                this.fetchPresets();

                if(this.isTwaePreset()){
                    const twaeLayoutControl='.elementor-control-twae_layout .elementor-control-content .elementor-control-input-wrapper select[data-setting="twae_layout"]';
                    jQuery(document).on('change',twaeLayoutControl,()=>{
                        const widgetName=this.getWidgetName();
                        if(widgetName && ['timeline-widget-addon', 'twae-post-timeline-widget'].includes(widgetName)){
                            const presetCls='.elementor-control.elementor-control-type-twae_preset_style';
                            const twaePresetControl=jQuery(presetCls).not('.elementor-hidden-control');
                            const presetFieldCls='.elementor-control-field select[data-setting="twae_preset_vertical_style"], .elementor-control-field select[data-setting="twae_preset_hr_style"]';
                            const twaePresetFields=twaePresetControl.find(presetFieldCls);
                            if(twaePresetFields.length > 0){
                                const presetValue=twaePresetFields.val();
                                if(null === presetValue){
                                    const hiddenField=jQuery(`${presetCls}.elementor-hidden-control`).find(presetFieldCls);
                                    const hiddenFieldValue=hiddenField.val();
                                    let fieldUpdatedValue='';
                                    switch(hiddenFieldValue){
                                        case 'v-style-5':
                                            fieldUpdatedValue='h-style-0';
                                            break;
                                        case 'v-style-6':
                                            fieldUpdatedValue='h-style-5';
                                            break;
                                        case 'h-style-5':
                                            fieldUpdatedValue='v-style-6';
                                            break;
                                        case "":
                                            break;
                                        default:
                                            fieldUpdatedValue=hiddenFieldValue.indexOf('h-style') !== -1 ? hiddenFieldValue.replace('h-style','v-style') :hiddenFieldValue.replace('v-style','h-style');
                                            break;
                                    }
                                        
                                    if('' !== fieldUpdatedValue){
                                        hiddenField.find('option').length > 0 && hiddenField.find('option').removeAttr('selected');
                                        hiddenField.val(null);
                                        const presets_list = this.getPresets();
                                        if(undefined !== presets_list[fieldUpdatedValue]){
                                            const presetFieldKey=twaePresetFields.data('setting');
                                            const updatePreset=presets_list[fieldUpdatedValue];
                                            updatePreset[presetFieldKey]=fieldUpdatedValue;
                                            this.applyPresets( updatePreset )
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            },
            getWidgetName: function() {

                return this.container.settings.get( "widgetType" );
            },
            isPresetFetched: function() {

                if( undefined !== window.twaePresets[this.getWidgetName()] ){

                    return window.twaePresets[this.getWidgetName()];
                } else {

                    return false;
                }
            },
            fetchPresets: function() {

                if( this.isTwaePreset() && !this.isPresetFetched() && this.getWidgetName() ){

                    var current_widget = this;

                    $.ajax({

                        url: twae_preset.ajaxUrl,
                        type: 'POST',
                        data: {
                            action: "twae_preset_feat",
                            widget: this.getWidgetName(),
                            nonce: twae_preset.nonce,                            
                        }
                    }).done( function( result ) {
                        
                        if( result.success ){
                            current_widget.setPresets( result.data );
                        }

                    });
                    
                }
            },
            setPresets: function( widget_json ) {
               
                window.twaePresets[this.getWidgetName()] = JSON.parse( widget_json );
            },
            getPresets: function() {
                    
                if( undefined !== window.twaePresets[this.getWidgetName()] ){
                    
                    return window.twaePresets[this.getWidgetName()];
                } else {
                    
                    return {};
                }
            },
            onBaseInputChange: function( event ) {
               
                this.constructor.__super__.onBaseInputChange.apply( this, arguments );

                if ( this.isTwaePreset() ) {

                    event.stopPropagation();

                    var presets_list = this.getPresets();
                  
                    if( "" == event.currentTarget.value ) {

                        if( undefined !== presets_list["default"] ) {
                            this.applyPresets( presets_list["default"] );  
                        } else {                            
                            this.defaultStyle( this.container.settings.defaults );   
                        }
                    } else if( undefined !== presets_list[event.currentTarget.value] ){

                        this.applyPresets( presets_list[event.currentTarget.value] );                               
                    }
                }
            },
            defaultStyle: function ( style ) {               
                this.applyPresets( style );
            },
            applyPresets: function ( presets_json ) {

                var e = elementor.getPanelView().getCurrentPageView().getOption( "editedElementView" );
             
                $e.run("document/elements/reset-style", {
                        container:e.getContainer()
                    }
                );

                var current_controls = this.container.settings.controls,
                    current_widget = this,
                    data_array = {},
                    settings = this.container.settings,
                    classControls = settings.getClassControls(),
                    current_widget_view = this.container.view.$el;

                var edited_controls = e.model._previousAttributes.settings._previousAttributes;

                _.each( current_controls, function ( current_control, controls_index ) {
                    
                    if ( current_widget.model.get( "name" ) !== controls_index && !_.isUndefined( presets_json[controls_index] ) ) {
                        
                        if ( current_control.is_repeater && current_control.default.length > 1 ) {

                            var cloned_widget = current_widget.container.settings.get( controls_index ).clone();
                            
                            cloned_widget.each( function ( current_control, data_array ) {
                                _.isUndefined(presets_json[controls_index][data_array]) || _.each( current_control.controls, function ( current_control, current_control_index ) {
                                        current_widget.isStyleTransferControl( current_control ) && cloned_widget.at( data_array ).set( current_control_index, presets_json[controls_index][data_array][current_control_index] );
                                    });
                            });

                            data_array[controls_index] = cloned_widget;

                            current_widget.isStyleTransferControl(current_control) && ( data_array[controls_index] = presets_json[controls_index] );


                        } else if( ( '' !== presets_json[controls_index] ) && current_widget.isContentTransferControl( current_control ) ) {
                            var edited_value = edited_controls[controls_index];

                            if( ( undefined !== typeof edited_value && '' !== edited_value && edited_value !== presets_json[controls_index] ) ) {
                                data_array[controls_index] = edited_value;
                            } else {
                                data_array[controls_index] = presets_json[controls_index];
                            }

                        } else {
                            data_array[controls_index] = presets_json[controls_index];
                        }
                    }
                });

                
                _.each(classControls, function (control) {

                    var previousClassValue = settings._previousAttributes[control.name];

                    if (control.classes_dictionary) {

                        if ( undefined !== control.classes_dictionary[previousClassValue] ) {
                            
                            previousClassValue = control.classes_dictionary[previousClassValue];
                        }
                    }

                    current_widget_view.removeClass(control.prefix_class + previousClassValue);
                });

                this.container.settings.setExternalChange( data_array );
                this.container.view.render();
            },
           
            isStyleTransferControl: function ( control ) {

                if ( undefined !== control.style_transfer ) {
                    return control.style_transfer;
                }

                return 'content' !== control.tab || control.selectors || control.prefix_class || control.return_value;
            },
           
            isContentTransferControl: function ( control ) {

                var control_type = control.type;

                if( 'text' === control_type || 'textarea' === control_type || 'icons' === control_type || 'wysiwyg' === control_type || 'media' === control_type || 'url' === control_type ) {

                    if ( true === control.style_transfer ) {
                        return false;
                    }

                    return true;
                }

                return false;
            }
        });
      
        elementor.addControlView( "twae_preset_style", preset_data );
       
    });
} )( jQuery ); 
