import AppForm from '../app-components/Form/AppForm';

Vue.component('modality-has-land-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                modality_id:  '' ,
                land_id:  '' ,
                
            }
        }
    }

});