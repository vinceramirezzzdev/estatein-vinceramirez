/**
 * Property gallery meta box: pick images with the media modal, drag to reorder.
 */
(function () {
  'use strict';

  document.querySelectorAll('[data-estatein-gallery]').forEach(function (box) {
    var list = box.querySelector('[data-gallery-list]');
    var input = box.querySelector('[data-gallery-input]');
    var frame;

    function sync() {
      input.value = Array.prototype.map.call(list.children, function (li) { return li.dataset.id; }).join(',');
    }

    function addItem(id, url) {
      var li = document.createElement('li');
      li.dataset.id = id;
      li.draggable = true;
      li.innerHTML = '<img alt="" src="' + url + '"><button type="button" class="estatein-gallery__remove" aria-label="Remove image">&times;</button>';
      list.appendChild(li);
    }

    box.querySelector('[data-gallery-add]').addEventListener('click', function () {
      if (!frame) {
        frame = wp.media({ title: 'Property gallery', button: { text: 'Add to gallery' }, multiple: true, library: { type: 'image' } });
        frame.on('select', function () {
          frame.state().get('selection').each(function (attachment) {
            var data = attachment.toJSON();
            var thumb = (data.sizes && data.sizes.thumbnail) ? data.sizes.thumbnail.url : data.url;
            addItem(data.id, thumb);
          });
          sync();
        });
      }
      frame.open();
    });

    list.addEventListener('click', function (e) {
      if (e.target.classList.contains('estatein-gallery__remove')) {
        e.target.closest('li').remove();
        sync();
      }
    });

    // Drag-and-drop reordering.
    var dragging = null;
    Array.prototype.forEach.call(list.children, function (li) { li.draggable = true; });
    list.addEventListener('dragstart', function (e) { dragging = e.target.closest('li'); });
    list.addEventListener('dragover', function (e) {
      e.preventDefault();
      var over = e.target.closest('li');
      if (over && dragging && over !== dragging) {
        var after = e.clientX > over.getBoundingClientRect().left + over.offsetWidth / 2;
        list.insertBefore(dragging, after ? over.nextSibling : over);
      }
    });
    list.addEventListener('drop', function () { dragging = null; sync(); });
  });
})();
