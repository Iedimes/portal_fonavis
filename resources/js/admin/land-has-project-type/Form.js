import AppForm from '../app-components/Form/AppForm';

Vue.component('land-has-project-type-form', {
    mixins: [AppForm],
    props:['land', 'pt'],
    data: function() {
        return {
            form: {
                land:  '' ,
                project_type:  '' ,

            }
        }
    }

});
