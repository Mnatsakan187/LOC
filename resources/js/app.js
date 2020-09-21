import Vue from 'vue'
import store from '~/store'
import router from '~/router'
import i18n from '~/plugins/i18n'
import Vuetify from 'vuetify'
import BootstrapVue from 'bootstrap-vue'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import VeeValidate from "vee-validate"
import VueMoment from 'vue-moment'
import Multiselect from 'vue-multiselect'
import * as moment from 'moment';
import VueLocalStorage from 'vue-localstorage'
import VueSession from 'vue-session'
import VueStripeCheckout from 'vue-stripe-checkout';
import datePicker from 'vue-bootstrap-datetimepicker';
import 'pc-bootstrap4-datetimepicker/build/css/bootstrap-datetimepicker.css';
import FullCalendar from 'vue-full-calendar'
import * as VueGoogleMaps from 'vue2-google-maps'
import { BPagination } from 'bootstrap-vue'
import vSelect from 'vue-select'
import VTooltip from 'v-tooltip'
var infiniteScroll =  require('vue-infinite-scroll');
import CKEditor from '@ckeditor/ckeditor5-vue';
import VueApexCharts from 'vue-apexcharts'


import Echo from "laravel-echo"
window.Pusher = require('pusher-js');
if(window.location.hostname == 'loc-develop.obiwansoft.com'){
  window.Echo = new Echo({
    broadcaster: 'pusher',
    key: 'cc1f102944939cc66f81',
    wsHost: 'loc-ws-develop.obiwansoft.com',
    wsPort: 6001,
    disableStats: true,
    cluster: 'eu',
    encrypted: true,
  });
}else if(window.location.hostname == 'loc-stage.obiwansoft.com'){
  window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '45320e6691f90c7bff21',
    wsHost: 'loc-ws-stage.obiwansoft.com',
    wsPort: 6002,
    disableStats: true,
    cluster: 'eu',
    encrypted: true,
  });
}

import '~/plugins'
import '~/shared'

Vue.use(infiniteScroll)
window.apiRoute = '/api/v1';
window.moment = require('moment');
window.$ = require('jquery');
var SocialSharing = require('vue-social-sharing');
Vue.use( CKEditor );

Vue.config.productionTip = false;
Vue.use(Vuetify);
Vue.use(BootstrapVue);
Vue.use(VeeValidate);
Vue.use(VueMoment, {
  moment,
});
Vue.component('multiselect', Multiselect);

Vue.use(VueLocalStorage)
Vue.use(VueSession);
Vue.use(VueStripeCheckout, 'pk_test_hO7fzh1xwJkS7eFMm2rTC0Ca00eetkdNy2');
Vue.use(datePicker);
Vue.use(FullCalendar);
Vue.component('b-pagination', BPagination)
Vue.component('v-select', vSelect)
Vue.use(SocialSharing);
Vue.use(VTooltip);
Vue.use(VueApexCharts);

VTooltip.options.defaultTemplate =
  '<div class="vue-tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'


Vue.use(VueGoogleMaps, {
  load: {
    key: 'AIzaSyCvE9xEo7_ctK8R9n8VCuz3ghWsiqRE8o4',
    libraries: 'places',
  },

})

window.bus = new Vue();


/* eslint-disable no-new */
new Vue({
  i18n,
  store,
  router,
  Vuetify,
  ...App
});

Vue.prototype.$eventHub = new Vue();


//Directive to detect click outside element
Vue.directive('click-outside', {
  bind: function (el, binding, vnode) {
    const event = function (event) {
      if (!(el == event.target || el.contains(event.target))) {
        vnode.context[binding.expression](event);
      }
    };
    document.body.addEventListener('click', event)
    document.body.addEventListener('touchstart', event)

  },
  unbind: function () {
    document.body.removeEventListener('click', event)
    document.body.removeEventListener('touchstart', event)
  },
});

import App from '~/shared/App'

import VueCropper from 'vue-cropperjs';
Vue.component(VueCropper);
Vue.component('apexchart', VueApexCharts)


window.cities  = [
  "Sydney",
  "South Melbourne",
  "Brisbane",
  "Perth",
  "Adelaide",
  "Newcastle",
  "Gold Coast",
  "Cranbourne",
  "Canberra",
  "Wollongong",
  "Geelong",
  "Cairns",
  "Townsville",
  "Albury",
  "Nowra",
  "Darwin",
  "Toowoomba",
  "Ballarat",
  "Bendigo",
  "Hobart",
  "North Mackay",
  "Mandurah",
  "Launceston",
  "Rockhampton",
  "Coffs Harbour",
  "Wagga Wagga",
  "Bundaberg",
  "Port Macquarie",
  "Mildura",
  "Taree",
  "Orange",
  "Caloundra",
  "West Tamworth",
  "Kalgoorlie",
  "Shepparton",
  "Mount Isa",
  "Tweed Heads",
  "Queanbeyan",
  "Melton",
  "Dubbo",
  "Caboolture",
  "North Lismore",
  "Gladstone",
  "Warrnambool",
  "Sunbury",
  "Alice Springs",
  "Geraldton",
  "Bunbury",
  "Albany",
  "Hervey Bay",
  "Mount Gambier",
  "Armidale",
  "Whyalla",
  "Sale",
  "Katoomba",
  "Goulburn",
  "Kwinana",
  "Burnie",
  "Echuca",
  "Devonport",
  "Roebourne",
  "East Maitland",
  "Traralgon",
  "Murray Bridge",
  "Forster",
  "Broken Hill",
  "Karratha",
  "Gawler",
  "Griffith",
  "Kingston Beach",
  "Port Hedland",
  "Ballina",
  "Wangaratta",
  "Port Augusta West",
  "Richmond North",
  "Singleton",
  "Bongaree",
  "Port Lincoln",
  "Broome",
  "Horsham",
  "Port Pirie",
  "Warwick",
  "Kempsey",
  "Portland",
  "Gympie South",
  "Muswellbrook",
  "Parkes",
  "Lithgow",
  "Bairnsdale East",
  "Bowen",
  "Yeppoon",
  "Batemans Bay",
  "Kiama",
  "Busselton",
  "Innisfail",
  "Katherine",
  "Moranbah",
  "South Grafton",
  "Dalby",
  "Charters Towers",
  "Emerald",
  "Swan Hill",
  "Ulladulla",
  "Colac",
  "Ayr",
  "Hamilton",
  "Kingaroy",
  "Inverell",
  "Moree",
  "Deniliquin",
  "Esperance",
  "Victor Harbor",
  "Young",
  "Carnarvon",
  "Leeton",
  "Gunnedah",
  "Narrabri West",
  "Stawell",
  "Atherton",
  "Cowra",
  "Byron Bay",
  "Tumut",
  "Cooma",
  "Pambula",
  "Biloela",
  "South Ingham",
  "Bathurst",
  "Ararat",
  "Wonthaggi",
  "Northam",
  "Kununurra",
  "Roma",
  "Mudgee",
  "Newman",
  "McMinns Lagoon",
  "Forbes",
  "Berri",
  "Cobram",
  "Scone",
  "Goondiwindi",
  "Manjimup",
  "Narrogin",
  "Smithton",
  "Proserpine",
  "Katanning",
  "Seymour",
  "Clare",
  "Central Coast",
  "Port Douglas",
  "Longreach",
  "Weipa",
  "Wallaroo",
  "Tom Price",
  "Merredin",
  "Bordertown",
  "Bourke",
  "North Scottsdale",
  "Queenstown",
  "Charleville",
  "Yamba",
  "Tumby Bay",
  "Mount Barker",
  "Peterborough",
  "Wagin",
  "Ceduna",
  "Kalbarri",
  "Penola",
  "Meningie",
  "Gingin",
  "Ouyen",
  "Port Denison",
  "Halls Creek",
  "Cloncurry",
  "Oatlands",
  "Winton",
  "Ravensthorpe",
  "Exmouth",
  "Barcaldine",
  "Streaky Bay",
  "Norseman",
  "Yulara",
  "Georgetown",
  "Pannawonica",
  "Pine Creek",
  "Meekatharra",
  "Kimba",
  "Boulia",
  "Onslow",
  "Quilpie",
  "Cowell",
  "Andamooka",
  "Eidsvold",
  "Woomera",
  "Wilcannia",
  "Mount Magnet",
  "Hughenden",
  "Laverton",
  "Richmond",
  "Birdsville",
  "Ivanhoe",
  "Morawa",
  "Theodore",
  "Adelaide River",
  "Leonora",
  "Kingston South East",
  "Thargomindah",
  "Burketown",
  "Three Springs",
  "Southern Cross",
  "Camooweal",
  "Bicheno",
  "Karumba",
  "Windorah",
  "Bedourie",
  "Kingoonya",
  "Melbourne",
  "Currie"
];

