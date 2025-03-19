const stripe = require('stripe')('sk_test_51PkQ4GRw4RJUah63IjAUmTN5UiIen2k2ioWbxbGWFBHLr2kW8W6SlLUVwlBVw9USXj0N1hI5yfRrK26aKJXwf3de00I8Yi5nEK'); // Usa tu clave secreta real aquí

exports.handler = async (event) => {
  try {
    const { items } = JSON.parse(event.body);

    if (!items || items.length === 0) {
      return { statusCode: 400, body: JSON.stringify({ error: "El carrito está vacío" }) };
    }

    const line_items = items.map(product => ({
      price_data: {
        currency: 'eur', // Se cambió a EUR (€)
        product_data: { name: product.name },
        unit_amount: product.price * 100 // Convertir a centavos
      },
      quantity: product.quantity || 1 // Manejo de cantidad de productos
    }));

    const session = await stripe.checkout.sessions.create({
      payment_method_types: ['card'],
      line_items,
      mode: 'payment',
      success_url: 'https://lite-chain.com',
      cancel_url: 'https://kinderdoge.xyz'
    });

    return { statusCode: 200, body: JSON.stringify({ id: session.id }) };
  } catch (error) {
    return { statusCode: 500, body: JSON.stringify({ error: error.message }) };
  }
};
