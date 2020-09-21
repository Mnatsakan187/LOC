export const helpers = {
  methods: {
    dataURLtoFile (dataurl, filename) {
      var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n)
      while (n--) {
        u8arr[n] = bstr.charCodeAt(n)
      }
      return new File([u8arr], filename, { type: mime })
    },

    updateProfilesColumn(profileId){
      axios.get(apiRoute + '/user/profiles/columns/'+profileId+'/updated', this.$store.getters['auth/token']).then(response => {

      }).catch(error => {

      })
    },

    markAllAsRead() {
      let _this = this;
      axios.post(apiRoute+ '/notifications/read').then(response => {
        _this.getNotifications()
      }).catch(function (error) {
      });
    },

    comparer(otherArray){
      return function(current){
      return otherArray.filter(function(other){
      return other.value == current.value && other.display == current.display
    }).length == 0;

  }
}
  },

  created () {

  }
}
