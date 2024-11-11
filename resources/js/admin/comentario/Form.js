import AppForm from '../app-components/Form/AppForm';

Vue.component('comentario-form', {
    mixins: [AppForm],
    props: ['postulante_id', 'cedula'], // Definiendo los props para recibir datos
    data: function() {
        return {
            form: {
                postulante_id: this.postulante_id, // Asignando el valor recibido a la propiedad del formulario
                cedula: this.cedula,                // Asignando el valor recibido a la propiedad del formulario
                comentario: ''
            }
        }
    },
    mounted() {
        // Imprimiendo los valores en la consola
        console.log('postulante_id:', this.postulante_id);
        console.log('cedula:', this.cedula);
    }
});
