import AppForm from '../app-components/Form/AppForm';

Vue.component('document-check-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                project_id:  '' ,
                document_id:  '' ,
                
            }
        }
    }

});