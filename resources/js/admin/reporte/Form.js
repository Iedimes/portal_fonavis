import AppForm from '../app-components/Form/AppForm';
import axios from 'axios';

Vue.component('reporte-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                inicio: '',
                fin: '',
                sat_id: '',
                state_id: '',
                city_id: '',
                modalidad_id: '',
                stage_id: '',
                proyecto_id: ''
            },
            cities: [] // Array para almacenar las ciudades
        }
    },
    watch: {
        'form.state_id': function(newStateId) {
            this.fetchCities(newStateId); // Llama a la función para obtener ciudades
        }
    },
    methods: {
        fetchCities(stateId) {
            if (stateId) {
                axios.get(`/admin/reportes/cities?state_id=${stateId}`)
                    .then(response => {
                        this.cities = response.data; // Actualiza el array de ciudades
                        this.form.city_id = ''; // Reinicia city_id cuando cambia el estado
                    })
                    .catch(error => {
                        console.error("Error fetching cities:", error);
                    });
            } else {
                this.cities = []; // Reinicia las ciudades si no hay estado seleccionado
            }
        }
    }
});