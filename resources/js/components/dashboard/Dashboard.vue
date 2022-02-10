<template>

    <div>
        <!-- <v-card elevation="2">
            <v-card-title
          >

           <v-icon>mdi-view-dashboard</v-icon> Dashboard

        </v-card-title>
        <v-card-text>

            <v-img src="../img/building.png" alt="Logo"  ></v-img>
        </v-card-text>
        </v-card> -->

        <v-card elevation="2" :loading="loading">
            <v-card-title
          >

           <v-icon>mdi-view-dashboard</v-icon> Dashboard
          <v-spacer></v-spacer>
          <v-col cols="auto">
              <v-text-field
              v-model="search"
              append-icon="mdi-magnify"
              label="Buscar"
              single-line
              hide-details
              filled
            rounded
            dense
          ></v-text-field>
          </v-col>

        </v-card-title>

            <v-card-text>
                <v-row>
                    <v-col cols="4">
                        <v-card>
                            <v-card-title>
                                Tipo Actividad vs Tiempo
                            </v-card-title>
                            <v-card-text>
                                <GChart
                                    type="PieChart"
                                    :data="chartDataTipo"
                                    :options="chartOptionsTipo"
                                />
                            </v-card-text>
                        </v-card>

                    </v-col>
                    <v-col cols="8">
                        <v-card>
                            <v-card-title>
                                Distribución diaria
                            </v-card-title>
                            <v-card-text>
                                <GChart
                                    type="ColumnChart"
                                    :data="chartDataFecha"
                                    :options="chartOptionsFecha"
                                />
                            </v-card-text>
                        </v-card>
                       <!--  <v-card>
                            <v-card-title>
                                Persona vs Tiempo
                            </v-card-title>
                            <v-card-text>
                                <GChart
                                    type="ColumnChart"
                                    :data="chartDataPersona"
                                    :options="chartOptionsPersona"
                                />
                            </v-card-text>
                        </v-card> -->

                    </v-col>
                    <v-col cols="12">
                        <v-card>
                            <v-card-title>
                                Modo Calendario
                                <v-spacer></v-spacer>
                                <v-autocomplete
                                    :items="usuarios"
                                    item-text="name"
                                    item-value="id"
                                    v-model="usuario"
                                    label="Persona"
                                    return-object
                                    @change="getDataCalendar"
                                ></v-autocomplete>
                            </v-card-title>
                            <v-card-text>
                                <GChart
                                :settings="{packages: ['calendar']}"
                                    type="Calendar"
                                    :data="chartDataCalendar"
                                    :options="chartOptionsCalendar"
                                />
                            </v-card-text>
                        </v-card>

                    </v-col>

                </v-row>


            </v-card-text>

        </v-card>


    </div>

</template>

<script>
import { GChart } from 'vue-google-charts'
export default {
    components: {
    GChart
  },
    data() {
        return {

            loading: true,
            usuarios:[],
            usuario:{},
            search:"",
            chartDataCalendar: [],
            chartOptionsCalendar: {
                height: 350,
                calendar: {
                dayOfWeekLabel: {
                    fontSize: 12,
                    bold: true,
                },
                dayOfWeekRightSpace: 10,
                daysOfWeek: 'DLMMJVS',
                }

            },
            chartDataPersona:[
                ['Usuario', 'Tiempo']
            ],
            chartOptionsPersona:{
                height: 300,
                fontSize:12,
                legend:{
                    position:'top',
                    maxLines:3,
                    alignment:'center'
                },


            },
            chartDataFecha:[
                ['Fecha', 'Horas']
            ],
            chartOptionsFecha:{
                height: 300,
                fontSize:12,
                legend:{
                    position:'top',
                    maxLines:3,
                    alignment:'center'
                },


            },
            chartDataTipo:[
                ['Tipo Actividad', 'Tiempo']
            ],
            chartOptionsTipo:{
                legend:{
                    position:'left',
                    maxLines:3,
                    alignment:'center'
                },
                fontSize:12,
                height: 300,
                pieHole: 0.4,
                chartArea:{
                    left:10,
                }
            },
            actividades:[]

        };
    },
    created() {


        this.getData();
    },
    methods: {
        getData(){
            this.axios.get(`/api/usuarios`).then(response => {
                this.usuarios = response.data;
                console.log(this.usuarios);
                //this.loading = false;
            });
            this.axios.get("/api/dashboard/").then(response => {
            this.actividades=response.data;
                console.log(this.actividades);
            response.data.forEach(element => {
                this.chartDataPersona.push([element.name,parseFloat(element.total)])
            });
            //this.chartDataPersona=this.chartDataPersona.sort(this.SortArray)

                    this.loading=false;

        });
        this.axios.get("/api/dashboardPorTipo/").then(response => {
            //this.actividades=response.data;
            console.log(response.data);
            response.data.forEach(element => {
                this.chartDataTipo.push([element.descripcion,parseFloat(element.total)])
            });

        });
        this.axios.get("/api/dashboardPorFecha/").then(response => {
            //this.actividades=response.data;
            console.log(response.data);
            response.data.forEach(element => {
                this.chartDataFecha.push([element.fecha,parseFloat(element.total)])
            });

        });


        },
        getDataCalendar(){
            console.log(this.usuario);
            this.chartDataCalendar=[];
            this.chartDataCalendar.push(['Fecha','Total']);

            this.axios.get(`/api/dashboardCalendario/${this.usuario.id}`).then(response => {
            console.log(response.data);
            response.data.forEach(element => {


                    this.chartDataCalendar.push([new Date(element.fecha),parseFloat(element.total)])

            });

        });
        },
        SortArray(x, y){
            if (x.total < y.total) {return -1;}
            if (x.total > y.total) {return 1;}
            return 0;
        }

    }
};
</script>
