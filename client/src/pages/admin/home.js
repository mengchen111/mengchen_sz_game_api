/**
 * Created by liudian on 9/14/17.
 */

import './common.js'

new Vue({
  el: '#app',
  data: {
    adminHomeApi: '/admin/api/home',
    hello: 'hi',
  },

  created: function () {

  },

  mounted: function () {
    let _self = this

    axios.get(this.adminHomeApi)
      .then(function (res) {
        if (res.data.error) {
          alert(res.data.error)
        }
        _self.hello = res.data.message
      })
  },
})