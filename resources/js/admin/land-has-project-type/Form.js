import AppForm from '../app-components/Form/AppForm';

Vue.component('land-has-project-type-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                land_id:  '' ,
                project_type_id:  '' ,
                
            }
        }
    }

});