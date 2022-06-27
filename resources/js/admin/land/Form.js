import AppForm from '../app-components/Form/AppForm';

Vue.component('land-form', {
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