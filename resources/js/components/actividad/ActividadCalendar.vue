<template>
    <div>
        <v-card elevation="2" >
            <v-card-title >
                Mi Actividad

                <v-spacer></v-spacer>
                <v-btn-toggle
                    v-model="icon"
                    borderless
                    dense

                >
                    <v-btn value="left" :to="{ name: 'actividad'}">
                    <span class="hidden-sm-and-down">Lista</span>

                    <v-icon right>
                        mdi-format-align-left
                    </v-icon>
                    </v-btn>

                    <v-btn value="center" :to="{ name: 'actividad-calendar'}">
                    <span class="hidden-sm-and-down">Calendario</span>

                    <v-icon right>
                        mdi-calendar
                    </v-icon>
                    </v-btn>

                </v-btn-toggle>
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
                <v-btn

                    color="primary"
                    small
                    :to="{
                        name: 'actividad-new'
                    }"
                >
                    <v-icon>mdi-plus</v-icon> NUEVO REGISTRO
                </v-btn>
            </v-card-title>

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
                        <v-btn icon>
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
            icon: 'justify',
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
        };
    },
    mounted () {
      this.$refs.calendar.checkChange()
    },
    created() {
        const query =
            ((this.$store.state.user.role == 2)//ADMIN
            || this.$store.state.user.role == 3) //SUPERVISOR
                ? `actividades`
                : `actividades/${this.$store.state.user.id}`;

                const temp=`actividades`
        this.axios.get(`/api/${query}`).then(response => {
            this.actividades = response.data;
            this.updateRange();
            console.log(this.actividades);
        this.loading = false;
        });
        this.firstLoad = false;
    },
    computed:{
        formatTime(value){
            if (value) {
                return moment(String(value)).format('hh:mm')
            }
        },
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

          const events = []
            for (const item of this.actividades) {
                //console.log(item);
                for (const i of item.actividades) {
                    const name =
                                ((this.$store.state.user.role == 2)//ADMIN
                                || this.$store.state.user.role == 3) //SUPERVISOR
                                    ? `${item.usuario.name} - ${i.descripcion}`
                                    : `${i.descripcion}`;

                    //this.categories=item.usuario.name;
                    const allDay = this.rnd(0, 3) === 0
                    events.push({
                        name: name,
                        start: (item.fecha+' '+i.h_inicio.substr(0, 5)),
                        end: (item.fecha+' '+i.h_fin.substr(0, 5)),
                        color: this.colors[this.rnd(0, this.colors.length - 1)],
                        timed: !allDay,
                        details: `Fecha: ${item.fecha}<br>
                                    Usuario: ${item.usuario.name}<br>
                                    Tipo Actividad: ${i.tipo.descripcion}<br>
                                Inicio: ${i.h_inicio.substr(0, 5)} <br>
                                Fin: ${i.h_fin.substr(0, 5)} <br>`,
                    })
                }
            }

        this.events = events
      },
      rnd (a, b) {
        return Math.floor((b - a + 1) * Math.random()) + a
      },


    }
};
</script>
