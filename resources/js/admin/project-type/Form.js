import AppForm from '../app-components/Form/AppForm';

Vue.component('project-type-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                name:  '' ,
                short_name:  '' ,
                
            }
        }
    }

});