import axios from 'axios';

Vue.component('legajo-masivo-form', {
    data: function () {
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
            showInicioAlert: false,
            showFinAlert: false,
            submiting: false
        }
    },
    watch: {
        'form.state_id': function (newStateId) {
            this.fetchCities(newStateId);
            if (newStateId === 0) {
                this.form.city_id = 0;
            }
        },
        'form.inicio': function (newVal) {
            if (newVal && !this.form.fin && !this.showFinAlert) {
                this.showFinAlert = true;
                alert('Debe seleccionar una fecha de fin si ha seleccionado una fecha de inicio.');
            }
        },
        'form.fin': function (newVal) {
            if (newVal && !this.form.inicio && !this.showInicioAlert) {
                this.showInicioAlert = true;
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
                        this.form.city_id = 0;
                    })
                    .catch(error => {
                        console.error("Error fetching cities:", error);
                    });
            } else {
                this.cities = [];
                this.form.city_id = 0;
            }
        },
        async onSubmit() {
            if ((this.form.inicio && !this.form.fin) || (!this.form.inicio && this.form.fin)) {
                alert('Debe seleccionar un rango completo de fechas.');
                return;
            }

            this.submiting = true;

            try {
                let formData = new FormData();
                for (let key in this.form) {
                    formData.append(key, this.form[key]);
                }

                const response = await axios.post(this.$el.getAttribute('action'), formData, {
                    responseType: 'blob'
                });

                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;

                // Obtener nombre de archivo desde header si existe
                const contentDisposition = response.headers['content-disposition'];
                let fileName = 'legajos_masivos.zip';
                if (contentDisposition) {
                    const match = contentDisposition.match(/filename="?(.+)"?/);
                    if (match && match.length === 2) fileName = match[1];
                }

                link.setAttribute('download', fileName);
                document.body.appendChild(link);
                link.click();
                link.remove();

            } catch (error) {
                console.error(error);
                let message = 'No se encontraron proyectos con los filtros seleccionados.';

                if (error.response && error.response.data instanceof Blob) {
                    const reader = new FileReader();
                    reader.onload = () => {
                        try {
                            const errorData = JSON.parse(reader.result);
                            if (errorData.error) {
                                message = errorData.error;
                            }
                        } catch (e) {
                            // No es JSON, usar mensaje por defecto
                        }
                        alert(message);
                    };
                    reader.readAsText(error.response.data);
                } else {
                    alert(message);
                }
            } finally {
                this.submiting = false;
            }
        }
    }
});
