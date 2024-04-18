import AppForm from '../app-components/Form/AppForm';

Vue.component('medium-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                model_type:  '' ,
                model_id:  '' ,
                uuid:  '' ,
                collection_name:  '' ,
                name:  '' ,
                file_name:  '' ,
                mime_type:  '' ,
                disk:  '' ,
                conversions_disk:  '' ,
                size:  '' ,
                manipulations:  this.getLocalizedFormDefaults() ,
                custom_properties:  this.getLocalizedFormDefaults() ,
                generated_conversions:  this.getLocalizedFormDefaults() ,
                responsive_images:  this.getLocalizedFormDefaults() ,
                order_column:  '' ,
                
            }
        }
    }

});