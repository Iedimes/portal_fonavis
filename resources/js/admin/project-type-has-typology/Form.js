import AppForm from '../app-components/Form/AppForm';

Vue.component('project-type-has-typology-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                project_type_id:  '' ,
                typology_id:  '' ,
                
            }
        }
    }

});