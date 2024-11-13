import AppForm from '../app-components/Form/AppForm';

Vue.component('motivo-form', {
    mixins: [AppForm],
    props: ['project_id'], // Definiendo los props para recibir datos
    data: function() {
        return {
            form: {
                project_id:  this.project_id ,
                motivo:  '' ,

            }
        }
    }

});
