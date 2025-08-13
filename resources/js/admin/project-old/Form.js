import AppForm from '../app-components/Form/AppForm';

Vue.component('project-old-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                project_id:  '' ,
                name:  '' ,
                
            }
        }
    }

});