import AppForm from '../app-components/Form/AppForm';
import axios from 'axios';

Vue.component('reporte-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                inicio: '',
                fin: '',
                proyecto_id: 0,
                sat_id: 0,
                state_id: 0,
                city_id: 0,
                modalidad_id: 0,
                stage_id: 0
            },
            cities: [],
            showInicioAlert: false, // Variable para controlar la alerta de inicio
            showFinAlert: false // Variable para controlar la alerta de fin
        }
    },
    watch: {
        'form.state_id': function(newStateId) {
            this.fetchCities(newStateId);
            if (newStateId === 0) {
                this.form.city_id = 0; // Reinicia city_id a "TODOS"
            }
        },
        'form.inicio': function(newVal) {
            if (newVal && !this.form.fin && !this.showFinAlert) {
                this.showFinAlert = true; // Marcar que se ha mostrado la alerta de fin
                alert('Debe seleccionar una fecha de fin si ha seleccionado una fecha de inicio.');
            }
        },
        'form.fin': function(newVal) {
            if (newVal && !this.form.inicio && !this.showInicioAlert) {
                this.showInicioAlert = true; // Marcar que se ha mostrado la alerta de inicio
                alert('Debe seleccionar una fecha de inicio si ha seleccionado una fecha de fin.');
            }
        }
    },
    methods: {
        fetchCities(stateId) {
            if (stateId && stateId !== 0) {
                axios.get(`/admin/reportes/cities?state_id=${stateId}`)
                    .then(response => {
                        this.cities = response.data;
                        this.form.city_id = 0; // Reinicia city_id a "TODOS" cuando se obtienen ciudades
                    })
                    .catch(error => {
                        console.error("Error fetching cities:", error);
                    });
            } else {
                this.cities = []; // Reinicia las ciudades si no hay estado seleccionado
                this.form.city_id = 0; // Reinicia city_id a "TODOS"
            }
        },
        isValidDateRange() {
            return (this.form.inicio && this.form.fin) || (!this.form.inicio && !this.form.fin);
        }
    }
});