<template>
    <div>
        <v-card elevation="2" :loading="loading">
            <v-card-title class="d-flex justify-space-between mb-6"
                >Resumen Contable
            </v-card-title>

            <v-card-text>
                <v-form ref="form" v-model="valid">
                     <v-row>
                        <v-col cols="12" sm="3">
                                     <v-menu
                                        v-model="menu"
                                        :close-on-content-click="false"
                                        :nudge-right="40"
                                        transition="scale-transition"
                                        offset-y
                                        min-width="auto"
                                    >
                                        <template v-slot:activator="{ on, attrs }">
                                            <v-text-field
                                                v-model="dateRangeText"
                                                label="Rango de fechas"
                                                prepend-icon="mdi-calendar"
                                                readonly
                                                v-bind="attrs"
                                                v-on="on"
                                                filled
                                                rounded
                                                dense
                                                single-line
                                                hide-details
                                            ></v-text-field>
                                        </template>
                                        <v-date-picker v-model="dates" range>
                                        </v-date-picker>
                                    </v-menu>


                        </v-col>
                        <v-col cols="12" sm="2">
                            <v-autocomplete
                                :items="areas"
                                item-text="nombre"
                                item-value="id"
                                v-model="area"
                                label="Seleccione un area"
                                return-object
                                rounded
                                filled
                                dense
                                single-line
                                @change="getUsers"
                            ></v-autocomplete>
                        </v-col>
                        <v-col cols="12" sm="5">
                            <v-autocomplete
                                deletable-chips
                                multiple
                                small-chips
                                clearable
                                :items="usuarios"
                                item-text="name"
                                item-value="id"
                                v-model="selectedUsuarios"
                                label="Seleccione uno o varios usuarios"
                                dense
                                single-line
                                rounded
                                filled
                                :disabled="dates.length<=1"
                                return-object

                            >
                                <template v-slot:prepend-item>
                                    <v-list-item ripple @click="toggle">
                                        <v-list-item-action>
                                            <v-icon
                                                :color="selectedUsuarios.length > 0 ? 'indigo darken-4' : ''">
                                                {{ icon }}
                                            </v-icon>
                                        </v-list-item-action>
                                        <v-list-item-content>
                                            <v-list-item-title>
                                                Seleccionar Todo
                                            </v-list-item-title>
                                        </v-list-item-content>
                                    </v-list-item>
                                    <v-divider class="mt-2"></v-divider>
                                </template>
                            </v-autocomplete>
                        </v-col>
                         <v-col cols="12" sm="2">
                            <v-btn
                                block
                                color="primary"
                                :loading="loadingUpload"
                                dark
                                :disabled="selectedUsuarios.length==0"
                                @click="getActividades"
                                >Generar Reporte</v-btn
                            >
                        </v-col>

                    </v-row>
                   <!--  <v-row align="center"
                        justify="center">
                        <v-col cols="12" class="m-auto">
                            <v-img
                                src="../img/building.png"
                                alt="Logo"
                                max-height="300px"
                                max-width="600px"
                            ></v-img>
                        </v-col>

                    </v-row> -->
                    <v-row>
                        <v-col cols="12">
                            <v-data-table
                            :headers="headers"
                            :items="actividades"
                            :search="search"
                        >
                             <template v-slot:item="row">
                                <tr>
                                    <td>{{ row.item.usuario.name }}</td>
                                    <td>{{ row.item.horas_p }}</td>
                                </tr>
                            </template>
                        </v-data-table>
                        </v-col>
                    </v-row>
                </v-form>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import moment from 'moment'
export default {
    data() {
        return {
            loading: false,
            loadingUpload: false,
            dates: [],
            valid: true,
            menu: false,
            firstLoad: true,
             actividades: [],
             search: "",
            usuarios: [],
            idUsuarios: [],
            selectedUsuarios: [],
            dateRules: [v => !!v || "Date range is required"],
            dataReport:{},
            areas : [],
            area:{},
            selectedAreas:[],
            idAreas: [],
            headers:[],
            personas:[]

        };
    },
    computed: {
        dateRangeText() {
            return this.dates.join(" ~ ");
        },
        likesAllUsers () {
        return this.selectedUsuarios.length === this.usuarios.length
      },
      likesSomeUsers () {
        return this.selectedUsuarios.length > 0 && !this.likesAllUsers
      },
      icon () {
        if (this.likesAllUsers) return 'mdi-close-box'
        if (this.likesSomeUsers) return 'mdi-minus-box'
        return 'mdi-checkbox-blank-outline'
      },
    },
    created() {
        this.initialData();
    },
    methods: {
        toggle() {
            this.$nextTick(() => {
                if (this.likesAllUsers) {
                    this.selectedUsuarios = [];
                } else {
                    this.selectedUsuarios = this.usuarios.slice();
                }
            });
        },
        initialData() {
            this.axios.get("/api/areas/").then(response => {
                this.areas = response.data;

            });

        },
        getUsers(){



            this.axios.get(`/api/usuarios-area/${this.area.id}`).then(response => {
                this.usuarios = response.data;
                console.log(this.usuarios);

                this.loading = false;
            });
        },
        async getActividades() {
            this.loading = true;
            this.loadingUpload = true;
            let url;
            this.idUsuarios = [];
            this.headers=[];
            this.selectedUsuarios.forEach(element => {
                this.idUsuarios.push(element.id)
                    });
            var fechaInicio=this.dates[0];
            var fechaFin=this.dates[1];
            this.headers.push({text:'Usuario',value:'usuario'})
            //this.headers.push({text:moment(fechaInicio).format("ddd, D MMM"),value:moment(fechaInicio).format("ddd, D MMM")})

            while (moment(fechaInicio).isSameOrBefore(moment(fechaFin))) {
                var auxFecha=moment(fechaInicio).format("ddd, D MMM");
                this.headers.push({text:auxFecha,value:auxFecha})
                fechaInicio=moment(fechaInicio).add(1,'days');

            }
              await this.axios
                .get(`/api/reporte-actividades-contable/${this.dates[0]}/${this.dates[1]}/${this.idUsuarios}`)
                .then(response => {
                    //this.headers.push({text:'Usuario',value:'usuario'})

                    //console.log(response.data);
                        this.actividades=response.data;
                        var horas=[];
                    this.actividades.forEach(element => {
                        horas.push(element.horas_p)

                        element.usuario.horas=element.horas_p;
                        //this.headers.push({text:moment(element.fecha).format("ddd, D MMM"),value:element.fecha})
                        const hora = parseInt((element.horas_p).substr(0, 2))
                        console.log(hora);
                    });
                    //this.personas.usuario=
                    console.log(this.actividades);
                    this.headers.push({text:'TOTAL',value:'total'})


                })
                .catch(error => console.log(error));

            this.loading = false;
            this.loadingUpload = false;
            //this.dates = [];
            //this.selectedUsuarios=[];
            this.valid = true;
        },
        async generarReporte() {
            this.loading = true;
            this.loadingUpload = true;
            let url;
            this.idUsuarios = [];
            this.selectedUsuarios.forEach(element => {
                this.idUsuarios.push(element.id)
                    });
            console.log(this.dataReport);
              await this.axios
                .get(`/api/reporte-actividades/${this.dates[0]}/${this.dates[1]}/${this.idUsuarios}`)
                .then(response => {
                    console.log(response);
                    url = response.config.baseURL + response.config.url;
                })
                .catch(error => console.log(error));

            this.loading = false;
            this.loadingUpload = false;
            this.dates = [];
            this.selectedUsuarios=[];
            this.valid = true;
            window.open(url, "_blank");
            this.initialData()
        }
    }
};
</script>

<style></style>
