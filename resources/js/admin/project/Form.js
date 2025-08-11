import AppForm from '../app-components/Form/AppForm';
import axios from 'axios';

Vue.component('project-form', {
    mixins: [AppForm],
    props: ['sat', 'modalidad', 'departamentos'],
    data() {
        return {
            form: {
                name: '',
                phone: '',
                sat_id: null,
                modalidad_id: '',
                land_id: '',
                typology_id: '',
                leader_name: '',
                state_id: '',
                city_id: '',
                localidad: '',
                action: '',
                expsocial: '',
                exptecnico: ''
            },
            tierraOptions: [],
            tipologiaOptions: [],
            localidadOptions: [],    // Ciudades dependientes de estado
            stateOptions: this.departamentos || [],  // Estados pasados desde blade
        };
    },
    watch: {
        'form.state_id'(newVal) {
            this.form.city_id = '';
            this.localidadOptions = [];

            if (newVal) {
                axios.get(`/projects/ajax/${newVal}/local`)
                    .then(response => {
                        // Convierte objeto {id:name} a array [{id, name}, ...]
                        this.localidadOptions = Object.entries(response.data).map(([id, name]) => ({ id, name }));
                    })
                    .catch(() => {
                        this.localidadOptions = [];
                    });
            }
        },
        'form.modalidad_id'(newVal) {
            this.form.land_id = '';
            this.form.typology_id = '';
            this.tierraOptions = [];
            this.tipologiaOptions = [];

            if (newVal) {
                axios.get(`/projects/ajax/${newVal}/lands`)
                    .then(response => {
                        this.tierraOptions = Object.entries(response.data).map(([id, name]) => ({ id, name }));
                    })
                    .catch(() => {
                        this.tierraOptions = [];
                    });
            }
        },
        'form.land_id'(newVal) {
            this.form.typology_id = '';
            this.tipologiaOptions = [];

            if (newVal) {
                axios.get(`/projects/ajax/${newVal}/typology`)
                    .then(response => {
                        this.tipologiaOptions = Object.entries(response.data).map(([id, name]) => ({ id, name }));
                    })
                    .catch(() => {
                        this.tipologiaOptions = [];
                    });
            }
        },
    }
});
