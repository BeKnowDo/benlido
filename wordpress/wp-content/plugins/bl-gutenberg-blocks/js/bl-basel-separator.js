wp.blocks.registerBlockType('bl-gutenberg-blocks/bl-basel-separator', {
    title: 'Basel Theme Separator',
    icon: '<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" role="img" aria-hidden="true" focusable="false"><path fill="none" d="M0 0h24v24H0V0z"></path><path d="M19 13H5v-2h14v2z"></path></svg>',
    category: 'bl-basel-gutenberg-blocks',
    attributes: {
      content: {type: 'string'},
      color: {type: 'string'}
    },
    
  /* This configures how the content and color fields will work, and sets up the necessary elements */

  /*
  <span class="title-separator"><span></span></span>
  */
    
    edit: function(props) {
      function updateContent(event) {
        props.setAttributes({content: event.target.value})
      }
      function updateColor(value) {
        props.setAttributes({color: value.hex})
      }

      const innerSpan = React.createElement("span", {});
      const outerSpan = React.createElement("span", {class: 'basel-title-style-cross title-separator'}, innerSpan);
      return outerSpan;
    },
    save: function(props) {
    const innerSpan = React.createElement("span", {});
      return wp.element.createElement(
        "span",
        { class: 'basel-title-style-cross title-separator' },
        innerSpan
      );
    }
  });