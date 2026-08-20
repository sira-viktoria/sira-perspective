define([
    'react',
    'react-dom'
], function (React, ReactDOM) {

    function MemeDisplay({ memes }) {
        if (!memes) {
            return React.createElement('span', null, 'Not Found');
        }

        if (!memes.selected) {
            return React.createElement('span', null, 'Not Selected');
        }

        const [isOpen, setIsOpen] = React.useState(false); // default modal state (disabled)
        return React.createElement(React.Fragment, null,
            React.createElement('img', {
                src: memes.selected,
                alt: 'Selected Meme',
                style: { maxHeight: '40px', display: 'block', cursor: 'pointer' },
                onClick: () => setIsOpen(true)
            }),
            isOpen && React.createElement('div', {
                style: {
                    position: 'fixed',
                    top: 0,
                    left: 0,
                    width: '100%',
                    height: '100%',
                    backgroundColor: 'rgba(0,0,0,0.7)',
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center',
                    zIndex: 9999,
                    cursor: 'pointer'
                },
                onClick: () => setIsOpen(false)
            }, React.createElement('img', {
                src: memes.selected,
                alt: 'Full Meme'
            }))
        );
    }

    return function (config, element) {
        const memesData = config.memes;
        const uid = element.id;
        ReactDOM.render(
            React.createElement(MemeDisplay, { memes: memesData }),
            document.getElementById(uid)
        );
    };
});
