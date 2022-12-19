<template>
    <div>
        <v-card elevation="2" :loading="loading">
            <v-card-title class="primary white--text text-h5"
                >Detalle de actividades
                <v-divider></v-divider>
                     <v-col cols="auto">
                            <v-btn-toggle
                                    borderless
                                    dense

                                >
                                    <v-btn

                                        color="red"
                                        :loading="loadingUpload"
                                        dark
                                        v-show="dates.length>=1 && selectedUsuarios.length > 0"
                                        @click="generarReporte"
                                        dense
                                        ><v-icon color="white">mdi-file-pdf-box</v-icon></v-btn
                                    >
                                    <v-btn

                                        color="green"
                                        :loading="loadingUpload"
                                        dark
                                        v-show="dates.length>=1 && selectedUsuarios.length > 0"
                                        @click="generarReporteXLSX"
                                        dense

                                        ><v-icon color="white">mdi-microsoft-excel</v-icon></v-btn
                                    >
                                    </v-btn-toggle>
                        </v-col>
            </v-card-title>
            <v-card-text>
                     <v-row class="pt-4">
                        <v-col cols="3" >
                             <v-menu
                                        v-model="menu"
                                        :close-on-content-click="false"
                                        :nudge-right="40"
                                        transition="scale-transition"
                                        offset-y
                                        min-width="auto"
                                        dense
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

                                                single-line
                                                hide-details
                                            ></v-text-field>
                                        </template>
                                        <v-date-picker v-model="dates" range>
                                        </v-date-picker>
                                    </v-menu>
                             <v-treeview
                                selectable
                                selected-color="red"
                                :items="personas"
                                dense
                                v-model="selectedUsuarios"
                                return-object
                                >
                                <template v-slot:prepend="{ item }">
                                    <v-icon v-if="!item.children">
                                    mdi-account
                                    </v-icon>
                                </template>
                            </v-treeview>
                        </v-col>
                        <v-divider vertical></v-divider>
                        <v-col
                                class="d-flex text-center pa-4"
                            >
                            <v-autocomplete

                                multiple
                                small-chips

                                :items="usuarios"
                                item-text="name"
                                item-value="id"
                                v-model="selectedUsuarios"
                                label="Usuarios seleccionados"
                                color="blue-grey lighten-2"

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
                    </v-row>
<br>

                <!-- <v-row v-if="data_actividades.length>0">
                        <v-data-table
                    :headers="headers"
                    :items="data_actividades"
                    :search="search"
                    class="elevation-2"
                    :loading="loading"
                    loading-text="Loading... Please wait"
                    dense
                    items-per-page="25"
                    :footer-props="{
                        showFirstLastPage: true,
                        firstIcon: 'mdi-arrow-collapse-left',
                        lastIcon: 'mdi-arrow-collapse-right',
                        prevIcon: 'mdi-minus',
                        nextIcon: 'mdi-plus'
                        }"
                >
                    <template v-slot:item="row">
                        <tr>
                            <td >
                                {{ row.item.usuario }}
                            </td>
                            <td>
                                {{ row.item.area }}
                            </td>
                            <td>
                                {{ row.item.puesto }}
                            </td>
                            <td>
                                {{ row.item.cliente }}
                            </td>
                            <td>
                                {{ row.item.tipo }}
                            </td>
                            <td>
                                {{ row.item.clasificacion }}
                            </td>
                            <td>
                                {{ row.item.fecha }}
                            </td>
                                  <td>{{ row.item.hora_inicio }} </td>
                                    <td>{{ row.item.hora_fin }} </td>
                                  <td>
                                      <strong class="primary--text">{{ row.item.total }}</strong>

                                 </td>
                             <td>
                                 {{ row.item.actividades }}

                            </td>
                        </tr>
                    </template>
                </v-data-table>
                    </v-row> -->
            </v-card-text>
        </v-card>


    </div>
</template>

<script>
import XLSX from 'xlsx/xlsx.js';
import moment from 'moment'
export default {
    data() {
        return {
            loading: false,
            loadingUpload: false,
            dates: [],
            valid: true,
            menu: false,
            usuarios: [],
            idUsuarios: [],
            selectedUsuarios: [],
            dateRules: [v => !!v || "Date range is required"],
            dataReport:{},
            personas:[],
            data_actividades:[],
            search: "",
             headers: [

                {
                    text: "Usuario",
                    value: "usuario"
                },
                {
                    text: "Area",
                    value: "area"
                },
                {
                    text: "Cargo",
                    value: "puesto"
                },

                {
                    text: "Cliente",
                    value: "cliente",
                },
                {
                    text: "Tipo",
                    value: "tipo",
                },
                {
                    text: "Clasificacion",
                    value: "clasificacion",
                },
                {
                    text: "Fecha",
                    value: "fecha",
                },
                {
                    text: "Inicio",
                    value: "hora_inicio",
                    sortable: false,
                    filterable: false,
                },
                {
                    text: "Fin",
                    value: "hora_fin",
                    sortable: false,
                    filterable: false,
                },
                {
                    text: "Horas",
                    value: "total",
                    filterable: false,
                    align: "start"
                },
                 {
                    text: "Actividades",
                    value: "actividades",
                    sortable: false,
                    filterable: false,
                    align: "start"
                }
            ],
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
            const query =
            ((this.$store.state.user.role == 2)//ADMIN
            || (this.$store.state.user.role == 3)) //SUPERVISOR
                ? `usuarios-all`
                : `user`;
            this.axios.get(`/api/${query}`).then(response => {
                this.usuarios = response.data;
                console.log(this.usuarios);
                var arrayN1 = [];
                var arrayN2 = [];
                var arrayCoordinador = [];
                if (response.data.length>1) {
                    response.data.forEach(element => {
                    if (element.puesto.id==1) { //N1
                        arrayN1.push({
                                id: element.id,
                                name: element.name,
                            })
                    } else if (element.puesto.id==3 || element.puesto.id==5 || element.puesto.id==7){ //N2
                        arrayN2.push({
                                id: element.id,
                                name: element.name,
                            })
                    } else if (element.puesto.id==4 || element.puesto.id==6 || element.puesto.id==8){ //COORDINADORES
                        arrayCoordinador.push({
                                id: element.id,
                                name: element.name,
                            })
                    }



                });
                } else {
                    if (response.data.puesto.id==1) { //N1
                        arrayN1.push({
                                id: response.data.id,
                                name: response.data.name,
                            })
                    } else if (response.data.puesto.id==3 || response.data.puesto.id==5 || response.data.puesto.id==7){ //N2
                        arrayN2.push({
                                id: response.data.id,
                                name: response.data.name,
                            })
                    } else if (response.data.puesto.id==4 || response.data.puesto.id==6 || response.data.puesto.id==8){ //COORDINADORES
                        arrayCoordinador.push({
                                id: response.data.id,
                                name: response.data.name,
                            })
                    }
                }


                this.personas = [

                        {
                            id:1,
                            name: 'Ingeniero Nivel 1',
                            children:arrayN1
                        },
                        {
                            id:2,
                            name: 'Ingeniero Nivel 2',
                            children:arrayN2
                        },
                        {
                            id:3,
                            name: 'Coordinador',
                            children:arrayCoordinador
                        }




                ]
                console.log(this.personas);

                this.loading = false;
            });
        },
        async generarReporte() {
            this.loading = true;
            this.loadingUpload = true;
            let url;
            this.idUsuarios = [];
            this.selectedUsuarios.forEach(element => {
                this.idUsuarios.push(element.id)
                    });
           // console.log(this.dataReport);
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
        },
        async generarReporteXLSX() {
            this.loading = true;
            this.loadingUpload = true;
            let url;
            this.idUsuarios = [];
                console.log(this.selectedUsuarios);
            this.selectedUsuarios.forEach(element => {
                    this.idUsuarios.push(element.id)
            });

            var diff = 0.0;
            var inicio = null;
            var fin = null;
              await this.axios
                .get(`/api/reporte-actividadesXLSX/${this.dates[0]}/${this.dates[1]}/${this.idUsuarios}`)
                .then(response => {
                    //console.log(response.data);
                    var data = [];
                    response.data.usuarios.forEach(element => { //recorre usuarios
                    //console.log(element.actividades);
                        if (element.actividades.length>0) {

                            element.actividades.forEach(item => {
                                //console.log(item.actividad.horario_id);
                                if (item.actividad.horario_id==3) { //si es horario N1 NOCTURNO 22:00 - 06:00
                                   // console.log(item.h_inicio);
                                    if (item.h_inicio=='22:00:00') {
                                        inicio = '00:00';
                                        fin = '08:00';
                                    }
                                }else{

                                    inicio=item.h_inicio;
                                    fin=item.h_fin;
                                }
                               // console.log(inicio+'+'+fin+'='+Math.abs((moment.duration(moment(fin,'HH:mm').diff(moment(inicio,'HH:mm'))).asHours())).toFixed(2));
                                var aux = {
                                    id: item.id,
                                    usuario: element.usuario.name,
                                    area: element.usuario.puesto.area.nombre,
                                    puesto: element.usuario.puesto.descripcion,
                                    cliente: (item.cliente) ? item.cliente : null,
                                    tipo: (item.tipo) ? item.tipo.descripcion : null,
                                    clasificacion: (item.clasif) ? item.clasif.nombre : null,
                                    fecha: item.actividad.fecha,
                                    hora_inicio: item.h_inicio,
                                    hora_fin: item.h_fin,
                                    total: Math.abs((moment.duration(moment(fin,'HH:mm').diff(moment(inicio,'HH:mm'))).asHours())).toFixed(2),
                                    // horas_p: item.actividad.horas_p,
                                    // horas_np: item.actividad.horas_np,
                                    // horas_total: item.actividad.horas_total,
                                    actividades: item.descripcion,
                                };
                                //console.log(aux);
                                data.push(aux);
                                //this.data_actividades.push(aux);
                        });
                        }
                    //console.log(data);
                    });
                    this.data_actividades=data;
                    var ws = XLSX.utils.json_to_sheet(data);
                    var wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, "Actividades");
                    XLSX.writeFile(wb, response.data.inicio+'_'+response.data.fin+"_actividades.xlsx");

                })
                .catch(error => console.log(error));

            this.loading = false;
            this.loadingUpload = false;
            this.dates = [];
            this.selectedUsuarios=[];
            this.valid = true;
            this.initialData()
        }
    }
};
</script>

<style></style>
