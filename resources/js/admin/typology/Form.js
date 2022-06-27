import AppForm from '../app-components/Form/AppForm';

Vue.component('typology-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                name:  '' ,
                
            }
        }
    }

});