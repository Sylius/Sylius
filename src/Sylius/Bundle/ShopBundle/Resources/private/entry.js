import './js/app';
import './scss/style.scss';

const imagesContext = require.context('./images', true, /\.(jpg|png|svg)$/);
imagesContext.keys().forEach(imagesContext);
