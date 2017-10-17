new Vue({
  el: '#sidebar',
  data: {
    uri: {
      home: {
        isActive: false,
      },
      system: {
        isActive: false,
        log: {
          isActive: false,
        },
      },
    },
  },

  methods: {},

  created: function () {
    let _self = this
    let accessedUri = location.href.match(/http:\/\/[\w.-]+\/admin\/([\w/-]+)/)[1]
      .replace('-', '_')
      .split('/')

    //被访问的页面的菜单项会被设置为active
    accessedUri.reduce(function (lastValue, currentValue) {
      lastValue[currentValue].isActive = true
      return lastValue[currentValue]
    }, _self.uri)
  },
})