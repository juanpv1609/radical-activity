<template>
    <div>
        <v-card elevation="2" :loading="loading">
            <v-card-title class="d-flex justify-space-between"
                >Resumen General
                <v-spacer></v-spacer>
                <v-col cols="auto" >
                        <v-btn-toggle
                        borderless

                    >
                        <v-btn

                            color="red"
                            :loading="loadingUpload"
                            dark
                            :disabled="selectedUsuarios.length==0"
                            @click="generarReporte"
                            >Reporte PDF</v-btn
                        >
                        <v-btn

                            color="green"
                            :loading="loadingUpload"
                            dark
                            :disabled="selectedUsuarios.length==0"
                            @click="generarReporteXLSX"
                            >Reporte XLSX</v-btn
                        >
                        </v-btn-toggle>
                        </v-col>
            </v-card-title>

            <v-card-text>
                <v-form ref="form" v-model="valid">
                     <v-row>
                        <v-col cols="12" sm="3">
                            <v-row>
                                <v-col cols="12">
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

                                                single-line
                                                hide-details
                                            ></v-text-field>
                                        </template>
                                        <v-date-picker v-model="dates" range>
                                        </v-date-picker>
                                    </v-menu>
                                </v-col>

                            </v-row>

                        </v-col>
                        <v-col cols="12" sm="9">
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
                </v-form>
            </v-card-text>
        </v-card>
    </div>
</template>

<script>
import XLSX from 'xlsx/xlsx.js';
import moment from 'moment';
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
            dataReport:{}
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
        },
        async generarReporteXLSX() {
            this.loading = true;
            this.loadingUpload = true;
            let url;
            this.idUsuarios = [];
            this.selectedUsuarios.forEach(element => {
                this.idUsuarios.push(element.id)
                    });
            console.log(this.dataReport);
            var diff = 0.0;
            var inicio = null;
            var fin = null;
              await this.axios
                .get(`/api/reporte-actividadesXLSX/${this.dates[0]}/${this.dates[1]}/${this.idUsuarios}`)
                .then(response => {
                    //console.log(response);
                    var data = [];
                    response.data.usuarios.forEach(element => { //recorre usuarios
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
                                    tipo: item.tipo.descripcion,
                                    fecha: item.actividad.fecha,
                                    hora_inicio: item.h_inicio,
                                    hora_fin: item.h_fin,
                                    total: Math.abs((moment.duration(moment(fin,'HH:mm').diff(moment(inicio,'HH:mm'))).asHours())).toFixed(2),
                                    // horas_p: item.actividad.horas_p,
                                    // horas_np: item.actividad.horas_np,
                                    // horas_total: item.actividad.horas_total,
                                    descripcion: item.descripcion,
                                };
                                data.push(aux);
                        });
                        }
                    //console.log(data);
                    });
                    /* make the worksheet */
                    var ws = XLSX.utils.json_to_sheet(data);

                    /* add to workbook */
                    var wb = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(wb, ws, "Actividades");

                    /* generate an XLSX file */
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
