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

        <v-card  :loading="loading" style="background:#F5F5F5;" outlined color="transparent" >
            <v-card-title
          >

           <v-icon>mdi-view-dashboard</v-icon> Dashboard
          <v-spacer></v-spacer>
              <v-col cols="auto" v-if="$store.state.user.role>=2">
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

                                single-line
                                hide-details
                            ></v-text-field>
                        </template>
                        <v-date-picker v-model="dates" range>
                        </v-date-picker>
                    </v-menu>
                </v-col>
                <v-col cols="auto" v-if="$store.state.user.role>=2" >
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
                <v-col cols="auto">
                    <v-btn v-if="selectedUsuarios.length > 0"

                        color="primary"
                        dark
                        >aplicar</v-btn>
                </v-col>


        </v-card-title>

            <v-card-text>
                <v-row dense>
                    <v-col cols="4">
                        <v-card>
                            <v-card-title>
                                Tiempo por tipo de actividad
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
                                Distribución diaria de actividades
                                <v-spacer></v-spacer>
                               <!--  <v-autocomplete
                                    :items="usuarios"
                                    item-text="name"
                                    item-value="id"
                                    v-model="usuario"
                                    label="Persona"
                                    return-object
                                    @change="getDataCalendar"
                                ></v-autocomplete> -->
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
                    <v-col cols="12">
                       <!--  <v-card>
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
                        </v-card> -->
                         <!-- <v-card>
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

            loading: false,
            loadingUpload: false,
            dates: [],
            valid: true,
            menu: false,
            idUsuarios: [],
            selectedUsuarios: [],
            dateRules: [v => !!v || "Date range is required"],
            usuarios:[],
            usuario:{},
            search:"",
            chartDataCalendar: [],
            chartOptionsCalendar: {
                calendar: {
                dayOfWeekLabel: {
                    fontSize: 12,
                    bold: true,
                },
                dayOfWeekRightSpace: 10,
                daysOfWeek: 'DLMMJVS',
                },
                fontSize:12,
                chartArea: {width: '100%',height: '100%'}

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
                ['Fecha', 'Horas Productivas']
            ],
            chartOptionsFecha:{
                fontSize:12,
                legend:{
                    position:'top',
                    maxLines:3,
                    alignment:'center'
                },
                chartArea: {width: '100%'}


            },
            chartDataTipo:[
                ['Tipo Actividad', 'Tiempo']
            ],
            chartOptionsTipo:{
                legend:{
                    position:'left',
                    alignment:'center'
                },
                fontSize:12,
                chartArea: {width: '100%',height: '100%'}

            },
            actividades:[]

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


        this.getData();
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
        getData(){
            const query =
            ((this.$store.state.user.role == 2)//ADMIN
            || (this.$store.state.user.role == 3)) //SUPERVISOR
                ? `usuarios-all`
                : `user`;
            this.axios.get(`/api/${query}`).then(response => {
                this.usuarios = response.data;
               // console.log(this.usuarios);

                this.loading = false;
            });
            /* this.axios.get("/api/dashboard/").then(response => {
                this.actividades=response.data;
                    //console.log(this.actividades);
                response.data.forEach(element => {
                    this.chartDataPersona.push([element.name,parseFloat(element.total)])
                });
                //this.chartDataPersona=this.chartDataPersona.sort(this.SortArray)

                        this.loading=false;

            }); */
        this.axios.get("/api/dashboardPorTipo/").then(response => {
            //this.actividades=response.data;
            //console.log('tipo  '+response.data);
            response.data.forEach(element => {
                this.chartDataTipo.push([element.descripcion,parseFloat(element.total)])
            });

        });
        this.axios.get("/api/dashboardPorFecha/").then(response => {
            //this.actividades=response.data;
            //console.log(response.data);
            response.data.forEach(element => {
                const arrayFecha = element.fecha.split('-');
                this.chartDataFecha.push([new Date(arrayFecha[0],arrayFecha[1]-1,arrayFecha[2]),parseFloat(element.total)])
            });

        });
        this.getDataCalendar();


        },
        getDataCalendar(){
            //console.log(this.usuario);
            this.chartDataCalendar=[];
            this.chartDataCalendar.push(['Fecha','Total']);

            this.axios.get(`/api/dashboardCalendario/${this.$store.state.user.id}`).then(response => {
            console.log(response.data);
            response.data.forEach(element => {
                console.log(element.fecha+': '+element.total);
                const arrayFecha = element.fecha.split('-');

                    this.chartDataCalendar.push([new Date(arrayFecha[0],arrayFecha[1]-1,arrayFecha[2]),parseFloat(element.total)])

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
