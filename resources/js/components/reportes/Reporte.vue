<template>
    <div>
        <v-card elevation="2" >
            <v-card-title
                >Resumen Contable

            </v-card-title>

            <v-card-text>
                <v-form ref="form" v-model="valid">
                     <v-row>
                         <v-col cols="12" sm="5">
                             <v-row dense>
                                 <v-col cols="12" sm="6">
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


                                                dense
                                                outlined

                                                hide-details
                                            ></v-text-field>
                                        </template>
                                        <v-date-picker v-model="dates" range
                                        :max="new Date().toISOString().substr(0, 10)"
                                        min="2021-10-18">
                                        </v-date-picker>
                                    </v-menu>


                                </v-col>
                                <v-col cols="12" sm="6" v-if="this.$store.state.user.role>1">
                                    <v-autocomplete
                                        :items="areas"
                                        item-text="nombre"
                                        item-value="id"
                                        v-model="area"
                                        label="Seleccione un area"
                                        return-object


                                        dense
                                        outlined

                                        @change="getUsers"
                                    ></v-autocomplete>
                                </v-col>
                             </v-row >
                             <v-row dense>
                                 <v-col cols="12" >
                                    <v-btn
                                        block
                                        color="primary"
                                        :loading="loadingUpload"
                                        dark
                                        :disabled="selectedUsuarios.length==0"
                                        @click="getActividades"
                                        >FILTRAR</v-btn
                                    >
                                </v-col>
                                <v-col cols="12">
                                    <v-btn
                                        block
                                        color="green"
                                        dark
                                        :disabled="actividades.length==0"
                                        @click="exportData">
                                        Exportar XLSX
                                    </v-btn>
                                </v-col>
                             </v-row>
                         </v-col>
                         <v-col cols="12" sm="7">
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
                                outlined



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
                    <v-row v-if="actividades.length>0">
                        <v-col cols="12">
                            <v-data-table
                            :headers="headers"
                            :items="actividades"
                            :search="search"
                            :loading="loading"
                    loading-text="Loading... Please wait"
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
                                    <td>
                                        {{ row.item.nombre }}
                                    </td>
                                    <td v-for="(item, i) in row.item.horas_p" v-bind:key="i">
                                        {{ item }}
                                    </td>
                                    <td>
                                        <v-chip small color="green" dark label class="mx-1">
                                            <strong>{{ row.item.total }}</strong></v-chip>

                                    </td>
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
import moment from 'moment';
import XLSX from 'xlsx/xlsx.js';
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
            if ((this.$store.state.user.role == 2)|| (this.$store.state.user.role == 3)) {
                this.axios.get("/api/areas/").then(response => {
                this.areas = response.data;

            });
            } else {
                this.getUsers()
            }


        },
        getUsers(){

            const query =
            ((this.$store.state.user.role == 2)//ADMIN
            || (this.$store.state.user.role == 3)) //SUPERVISOR
                ? `usuarios-area/${this.area.id}`
                : `user`;
            this.axios.get(`/api/${query}`).then(response => {
                this.usuarios = response.data;
                console.log(this.usuarios);

                //this.loading = false;
            });
        },
        async getActividades() {
            //this.loading = true;
            this.loadingUpload = true;
            let url;
            this.idUsuarios = [];
            this.headers=[];
            this.selectedUsuarios.forEach(element => {
                this.idUsuarios.push(element.id)
                    });
            var fechaInicio=this.dates[0];
            var fechaFin=this.dates[1];
            this.headers.push({text:'Usuario',value:'usuario.nombre',sortable: true})
            //this.headers.push({text:moment(fechaInicio).format("ddd, D MMM"),value:moment(fechaInicio).format("ddd, D MMM")})
            var arrayFechas=[];
            while (moment(fechaInicio).isSameOrBefore(moment(fechaFin))) {
                var auxFecha=moment(fechaInicio).format("D MMM");
                arrayFechas.push(moment(fechaInicio).format("YYYY-MM-DD"));
                this.headers.push({text:auxFecha,value:auxFecha,sortable: false})
                fechaInicio=moment(fechaInicio).add(1,'days');

            }
            this.headers.push({text:'TOTAL',value:'total',sortable: true})
            //console.log(arrayFechas);
              await this.axios
                .get(`/api/reporte-actividades-contable/${arrayFechas}/${this.idUsuarios}`)
                .then(response => {
                    //this.headers.push({text:'Usuario',value:'usuario'})

                    //console.log(response.data);
                        this.actividades=response.data;
                        var horas=[];
                        //console.log(this.actividades);
                 this.actividades.forEach(element => {
                     var total=0.0;
                       // console.log(element.horas_p)
                        element.horas_p.forEach(hora => {

                            var parsed = hora.split(':')
                            var horas = (parsed[0]!="--") ? parseFloat(parsed[0]) : 0;
                            var minutos;
                            if (parsed[1]=="--") {

                                minutos = 0;
                            } else if(parsed[1]=="00"){
                                minutos = 0;
                            }else{
                                minutos = parseFloat(parsed[1])
                            }
                            //total = total + parseFloat(parseFloat(horas*60) + minutos);
                            total = total + parseFloat(horas*60)+parseFloat(minutos);
                            element.total=parseInt(total/60)+'.'+parseInt(total%60);
                        })
                        //var inicial = moment.duration(moment(element.horas_p,'HH:mm'));

                        //total=moment.duration(inicial).add(moment.duration(moment(element.horas_p,'HH:mm')))
                        //this.headers.push({text:moment(element.fecha).format("ddd, D MMM"),value:element.fecha})
                        //const hora = parseInt((element.horas_p).substr(0, 2))
                        //console.log(_total);
                    });
                    //console.log(this.actividades);
                    //this.personas.usuario=
                    //console.log(total);


            this.loading = false;
                })
                .catch(error => console.log(error));

            this.loadingUpload = false;
            //this.dates = [];
            //this.selectedUsuarios=[];
            this.valid = true;
        },
        exportData(){
            //var data = [];
            console.log(this.headers);
            console.log(this.actividades);
            var data = [];
            var header = [];
            this.headers.forEach(element => {

                header.push(element.text)
            });
            for (let index = 0; index < header.length; index++) {
                const element = header[index];
                var aux = {}

            }
                this.actividades.forEach(item => {
                    data.push(item.nombre)
                    item.horas_p.forEach(element => {
                        data.push(element)

                    });
                    data.push(item.total)
                });


            console.log(header);
            console.log(data);
            // var data = this.actividades.map((item) => {
            //             return {
            //                 id: item.id,
            //                 usuario: item.usuario.name,
            //                 fecha: item.fecha,
            //                 hora_inicio: item.hora_inicio,
            //                 hora_fin: item.hora_fin,
            //                 horas_p: item.horas_p,
            //                 horas_np: item.horas_np,
            //                 horas_total: item.horas_total,
            //                 horario: item.horario.nombre,
            //                 actividades: item.actividades_count,
            //                 created_at: item.created_at,
            //             }
            //         });
            /* this line is only needed if you are not adding a script tag reference */
            //if(typeof XLSX == 'undefined') XLSX = require('xlsx/xlsx.js');

            /* make the worksheet */
            var ws = XLSX.utils.json_to_sheet(data,{header:header});

            /* add to workbook */
            var wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "Actividades");

            /* generate an XLSX file */
            XLSX.writeFile(wb, this.dates[0]+"_"+this.dates[1]+"_ResumenActividades.xlsx");
        },
        async generarReporte() {
            this.loading = true;
            this.loadingUpload = true;
            let url;
            this.idUsuarios = [];
            this.selectedUsuarios.forEach(element => {
                this.idUsuarios.push(element.id)
                    });
            //console.log(this.dataReport);
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
