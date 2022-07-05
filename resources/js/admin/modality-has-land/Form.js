import AppForm from '../app-components/Form/AppForm';

Vue.component('modality-has-land-form', {
    mixins: [AppForm],
    props:['modality','land'],
    data: function() {
        return {
            form: {
                modality:  '' ,
                land:  '' ,

            }
        }
    }

});
