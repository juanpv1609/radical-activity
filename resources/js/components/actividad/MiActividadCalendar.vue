<template>
    <div>
        <div class="d-flex justify-content-center pb-2">
            <v-btn-toggle
                    v-model="icon"
                    borderless
                    dense
                    active-class="primary--text"

                >
                    <v-btn value="left" :to="{ name: 'mi-actividad'}">
                    <span class="hidden-sm-and-down">Lista</span>

                    <v-icon right>
                        mdi-format-align-left
                    </v-icon>
                    </v-btn>

                    <v-btn value="center" :to="{ name: 'mi-actividad-calendar'}">
                    <span class="hidden-sm-and-down">Calendario</span>

                    <v-icon right>
                        mdi-calendar
                    </v-icon>
                    </v-btn>

                </v-btn-toggle>
                <v-spacer></v-spacer>
                <div v-for="tipo in tipos" :key="tipo.id" >

                    <v-tooltip bottom :color="tipo.color">
                        <template v-slot:activator="{ on, attrs }">

                            <v-icon :color="tipo.color" v-bind="attrs"
                            v-on="on">mdi-circle</v-icon>
                        </template>
                    <span>{{ tipo.descripcion }}</span>
                    </v-tooltip>
                </div>
        </div>


        <v-card elevation="2" :loading="loading" >

            <v-card-text>

                <v-row class="fill-height">
                <v-col>
                <v-sheet height="64">
                    <v-toolbar
                    flat
                    >
                    <v-btn
                        outlined
                        class="mr-4"
                        color="grey darken-2"
                        @click="setToday"
                    >
                        Hoy
                    </v-btn>
                    <v-btn
                        fab
                        text
                        small
                        color="grey darken-2"
                        @click="prev"
                    >
                        <v-icon small>
                        mdi-chevron-left
                        </v-icon>
                    </v-btn>
                    <v-btn
                        fab
                        text
                        small
                        color="grey darken-2"
                        @click="next"
                    >
                        <v-icon small>
                        mdi-chevron-right
                        </v-icon>
                    </v-btn>
                    <v-toolbar-title v-if="$refs.calendar">
                        {{ $refs.calendar.title }}
                    </v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-menu
                        bottom
                        right
                    >
                        <template v-slot:activator="{ on, attrs }">
                        <v-btn
                            outlined
                            color="grey darken-2"
                            v-bind="attrs"
                            v-on="on"
                        >
                            <span>{{ typeToLabel[type] }}</span>
                            <v-icon right>
                            mdi-menu-down
                            </v-icon>
                        </v-btn>
                        </template>
                        <v-list>
                        <v-list-item @click="type = 'day'">
                            <v-list-item-title>Dia</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="type = 'week'">
                            <v-list-item-title>Semana</v-list-item-title>
                        </v-list-item>
                        <v-list-item @click="type = 'month'">
                            <v-list-item-title>Mes</v-list-item-title>
                        </v-list-item>
                        </v-list>
                    </v-menu>
                    </v-toolbar>
                </v-sheet>
                <v-sheet height="700">
                    <v-calendar
                    ref="calendar"
                    v-model="focus"
                    color="primary"
                    :events="events"
                    :event-color="getEventColor"
                    :type="type"
                    @click:event="showEvent"
                    @click:more="viewDay"
                    @click:date="viewDay"
                    @change="updateRange"
                    ></v-calendar>
                    <v-menu
                    v-model="selectedOpen"
                    :close-on-content-click="false"
                    :activator="selectedElement"
                    offset-x
                    >
                    <v-card
                        color="grey lighten-4"
                        min-width="300px"
                        flat
                    >
                        <v-toolbar
                        :color="selectedEvent.color"
                        dark
                        >
                        <v-toolbar-title v-html="selectedEvent.name"></v-toolbar-title>
                        <v-spacer></v-spacer>
                        <v-btn icon :disabled="$store.state.user.id!=selectedEvent.usuario" :to="{
                                        name: 'actividad-edit',
                                        params: { id: selectedEvent.id }
                                    }" title="Editar Actividad">
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                        </v-toolbar>
                        <v-card-text>
                        <span v-html="selectedEvent.details"></span>
                        </v-card-text>
                        <v-card-actions>
                        <v-btn
                            text
                            :color="selectedEvent.color"
                            @click="selectedOpen = false"
                        >
                            Cerrar
                        </v-btn>
                        </v-card-actions>
                    </v-card>
                    </v-menu>
                </v-sheet>
                </v-col>
            </v-row>
            </v-card-text>

        </v-card>

    </div>
</template>

<script>
import moment from 'moment'

export default {
    data() {
        return {
            firstLoad: true,
            //icon: 'justify',
            actividades: [],
            detalleActividades: [],
            update: false,
            persona: {},
            loading: true,
            search: "",
            valid: true,
            title:"",
            today:null,
      focus: '',
      type: 'month',
      typeToLabel: {
        month: 'Mes',
        week: 'Semana',
        day: 'Dia',
      },
      selectedEvent: {},
      selectedElement: null,
      selectedOpen: false,
      events: [],
      colors: ['blue', 'indigo', 'deep-purple', 'cyan', 'green', 'orange', 'grey darken-1','red accent-2','light-blue lighten-2','teal lighten-2',
      'light-green darken-2','lime lighten-1','green accent-4','yellow accent-3','orange accent-4','brown lighten-1','deep-orange lighten-2',
      'blue-grey darken-1'],
        categories:[],

        tipos: [],
        usuarios: [],
        idUsuarios: [],
        selectedUsuarios: [],
        };
    },
    computed: {
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
      formatTime(value){
            if (value) {
                return moment(String(value)).format('hh:mm')
            }
        },
    },
    mounted () {
      this.$refs.calendar.checkChange()
    },
    created() {
        //this.loading = true;
        this.axios.get("/api/tipo-actividad/").then(response => {
            this.tipos = response.data;

        });



        const query2 =`mi-actividad-calendar/${this.$store.state.user.id}`;

                this.axios.get(`/api/${query2}`).then(response => {
                    this.actividades = response.data;
                    console.log(this.actividades);
                    this.updateRange();
                this.loading = false;
                });
                this.firstLoad = false;
    },
    methods: {

        findActividades(el) {
            console.log(el);
             this.axios.get(`/api/detalle-actividades/${el.id}`).then(res => {
                //this.contratos = res.data;
                //console.log(res.data);
                this.detalleActividades = res.data;
                this.title = el.usuario.name+': '+el.fecha;
                $("#exampleModal2").modal("show");
                //this.dialogTareas=true;
            });
        },
        viewDay ({ date }) {
        this.focus = date
        this.type = 'day'
      },
      getEventColor (event) {
        return event.color
      },
      setToday () {
        this.today = new Date();
        console.log(this.today);
        this.focus = this.today.toLocaleDateString();
        this.type = 'day'
      },
      prev () {
        this.$refs.calendar.prev()
      },
      next () {
        this.$refs.calendar.next()
      },
      showEvent ({ nativeEvent, event }) {
        const open = () => {
          this.selectedEvent = event
          this.selectedElement = nativeEvent.target
          setTimeout(() => {
            this.selectedOpen = true
          }, 10)
        }

        if (this.selectedOpen) {
          this.selectedOpen = false
          setTimeout(open, 10)
        } else {
          open()
        }

        nativeEvent.stopPropagation()
      },
      updateRange () {
          //console.log(this.selectedUsuarios);

          const events = []
            for (const item of this.actividades) {
                //console.log(item);
                for (const i of item.actividades) {


                    //this.categories=item.usuario.name;
                    const allDay = this.rnd(0, 3) === 0
                    events.push({
                        id: item.id,
                        name: i.descripcion,
                        usuario: item.usuario.id,
                        start: (item.fecha+' '+i.h_inicio.substr(0, 5)),
                        end: (item.fecha+' '+i.h_fin.substr(0, 5)),
                        color: (i.tipo) ? i.tipo.color : 'teal lighten-3',
                        timed: !allDay,
                        details: `Fecha: ${item.fecha}<br>
                                Usuario: ${item.usuario.name}<br>
                                Clasificación: ${(i.clasif) ? i.clasif.nombre : null}<br>
                                Tipo Actividad: ${(i.tipo) ? i.tipo.descripcion : null}<br>
                                Descripción: ${i.descripcion}<br>
                                Inicio: ${i.h_inicio.substr(0, 5)} <br>
                                Fin: ${i.h_fin.substr(0, 5)} <br>`,
                    })
                }
            }

        this.events = events
      },
      updateRangeFilter () {
          this.loading = true;
          console.log(this.selectedUsuarios);
            var filtered_array=[];
            var auxActividades = this.actividades;
               this.selectedUsuarios.forEach(element => {
                    auxActividades.filter(function (item) {
                        if (item.usuario.name == element.name) {
                            filtered_array.push(item)
                        }
                    });
                    //this.actividades.push(filtered_array);
                //console.log(filtered_array);
            });
        console.log(filtered_array);
        //this.actividades = filtered_array;
           const events = []
            for (const item of filtered_array) {
                //console.log(item);
                for (const i of item.actividades) {
                    const name =
                                ((this.$store.state.user.role == 2)//ADMIN
                                || this.$store.state.user.role == 3) //SUPERVISOR
                                    ? `${item.usuario.name} - ${i.tipo.descripcion}`
                                    : `${i.tipo.descripcion}`;

                    //this.categories=item.usuario.name;
                    const allDay = this.rnd(0, 3) === 0
                    events.push({
                        id: item.id,
                        name: name,
                        usuario: item.usuario.id,
                        start: (item.fecha+' '+i.h_inicio.substr(0, 5)),
                        end: (item.fecha+' '+i.h_fin.substr(0, 5)),
                        color: i.tipo.color,
                        timed: !allDay,
                        details: `Fecha: ${item.fecha}<br>
                                Usuario: ${item.usuario.name}<br>
                                Tipo Actividad: ${i.tipo.descripcion}<br>
                                Descripcion: ${i.descripcion}<br>
                                Inicio: ${i.h_inicio.substr(0, 5)} <br>
                                Fin: ${i.h_fin.substr(0, 5)} <br>`,
                    })
                }
            }

        this.events = events;
        this.loading = false;
      },

      rnd (a, b) {
        return Math.floor((b - a + 1) * Math.random()) + a
      },


    }
};
</script>
