import './js/app';
import './scss/style.scss';

const imagesContext = require.context('./images', true, /\.(jpg|jpeg|png|svg)$/);
imagesContext.keys().forEach(imagesContext);
