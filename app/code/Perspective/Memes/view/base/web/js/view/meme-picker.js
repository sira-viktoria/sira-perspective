define([
    'react',
    'react-dom',
    'react-image-picker',
    'jquery'
], function(React, ReactDOM, ImagePicker, $) {
    'use strict';

    return function(config, element) {
        if (!element) return;

        ImagePicker = ImagePicker.default || ImagePicker;

        const memesData = config.memes;
        const notSelectedSrc = require.toUrl('Perspective_Memes/images/no-image-selected.webp');
        const quoteId = config.quoteId;

        const images = [
            { src: notSelectedSrc, value: -1, label: 'Not Selected' },
            ...memesData.items.map((src, i) => ({ src, value: i }))
        ];

        let initialIndex = 0;
        if (memesData.selected) {
            const memeIndex = memesData.items.findIndex(src => src === memesData.selected);
            if (memeIndex !== -1) {
                initialIndex = memeIndex + 1;
            }
        }

        function MemePicker() {
            const [selected, setSelected] = React.useState(images[initialIndex]);

            function onPick(image) {
                setSelected(image);

                const memeUrl = image.value === -1 ? null : image.src;
                $.ajax({
                    url: '/memes/ajax/updateselectedmeme',
                    type: 'POST',
                    dataType: 'json',
                    showLoader: true,
                    data: {
                        maskedQuoteId: quoteId,
                        selected: memeUrl,
                        entityType: 'quote'
                    },
                    success: function (res) {
                        if (res.success) {
                            console.log('Selected meme saved');
                        }
                    },
                    error: function (err) {
                        console.error('Selected meme error', err);
                    }
                });
            }

            return React.createElement('div', null,
                React.createElement(ImagePicker, {
                    images: images,
                    onPick: onPick,
                    multiple: false
                })
            );
        }

        ReactDOM.render(
            React.createElement(MemePicker),
            element,
            function() {
                setTimeout(() => {
                    const imgs = element.querySelectorAll('img');
                    if (memesData.selected) {
                        const idx = memesData.items.findIndex(src => src === memesData.selected) + 1;
                        imgs[idx] && imgs[idx].click();
                    } else {
                        imgs[0] && imgs[0].click(); // Not Selected
                    }
                }, 0);
            }
        );
    };
});
