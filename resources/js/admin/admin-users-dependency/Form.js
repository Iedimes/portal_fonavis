import AppForm from '../app-components/Form/AppForm';

Vue.component('admin-users-dependency-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                admin_user_id:  '' ,
                dependency_id:  '' ,
                
            }
        }
    }

});