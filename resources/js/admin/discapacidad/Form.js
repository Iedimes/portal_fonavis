import AppForm from '../app-components/Form/AppForm';

Vue.component('discapacidad-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                name:  '' ,
                
            }
        }
    }

});