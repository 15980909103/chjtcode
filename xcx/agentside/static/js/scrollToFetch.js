function scrollToFetch(panelSelector, ListSelector, callback) {
  const panel = $(panelSelector);
  panel.on('scroll', function() {
    const distance = getScrollDistance(panelSelector, ListSelector);
    if (distance < 110) {
      callback();
    }
  });
}

function getScrollDistance(panelSelector, ListSelector) {
    const panel = $(panelSelector);
    const list = panel.find(ListSelector);
    const position = list.css('transform').match(/\-\d+/);
    if (position) {
      const scroll = - Number(position[0]);
      const viewHeight = panel.height();
      const listHeight = list.height();
      return listHeight - viewHeight - scroll;
    }
    return 1000;
}
