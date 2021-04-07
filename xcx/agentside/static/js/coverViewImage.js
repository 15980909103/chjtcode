function openViewImage(options) {
  if (!checkViewPanel()) {
    createViewPanel();
  } else {
    toggleDisplay();
  }
  setViewCoverSrc(options.src);
}

function getMarsk() {
  return  document.querySelector('.marsk');
}

function createViewPanel() {
  const marsk = document.createElement('div');
  marsk.className = 'marsk cover-image';
  const cover = document.createElement('img');
  cover.className = 'cover';
  marsk.appendChild(cover);
  document.body.appendChild(marsk);
  setViewPanelStyle();
  addCloseListen();
}

function setViewPanelStyle() {
  const marskStyle = `
    .marsk.cover-image {
      position: fixed;
      left: 0;
      top: 0;
      background: rgba(0,0,0,0.4);
      width: 100vw;
      height: 100vh;
      text-align: center;
      z-index: 100;
    }
  `;
  const marskAfterStyle = `
    .marsk.cover-image:before {
      content: "";
      height: 100%;
    }
  `;
  const publicStyle = `
    .marsk.cover-image:before, .marsk.cover-image img{
      display:inline-block;
      vertical-align:middle;
    }
  `;
  const coverStyle = `
    .marsk.cover-image .cover {
      margin: 0 auto;
      height: auto;
      max-width: 80vw
    }
  `;
  document.styleSheets[0].insertRule(marskStyle, 0);
  document.styleSheets[0].insertRule(publicStyle, 0);
  document.styleSheets[0].insertRule(marskAfterStyle, 0);
  document.styleSheets[0].insertRule(coverStyle, 0);
}

function addCloseListen() {
  const marsk = getMarsk();
  marsk.addEventListener('touchstart', toggleDisplay, false);
}

function toggleDisplay() {
  const marsk = getMarsk();
  const display = marsk.style.display;
  marsk.style.display = window.getComputedStyle(marsk, null).getPropertyValue('display') == 'block'? 'none': 'block';
}

function setViewCoverSrc(src) {
  const cover = document.querySelector('.marsk .cover');
  cover.setAttribute('src', src);
}

function checkViewPanel() {
  return getMarsk();
}
