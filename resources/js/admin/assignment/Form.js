import AppForm from '../app-components/Form/AppForm';

Vue.component('assignment-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                document_id:  '' ,
                category_id:  '' ,
                project_type_id:  '' ,
                stage_id:  '' ,
                
            }
        }
    }

});