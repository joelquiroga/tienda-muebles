const stripe = require('stripe')('sk_test_51PkQ4GRw4RJUah63IjAUmTN5UiIen2k2ioWbxbGWFBHLr2kW8W6SlLUVwlBVw9USXj0N1hI5yfRrK26aKJXwf3de00I8Yi5nEK'); // Usa tu clave secreta real aquí

exports.handler = async (event) => {
  try {
    const session = await stripe.checkout.sessions.create({
      payment_method_types: ['card'],
      mode: 'payment',
      line_items: [
        {
          price_data: {
            currency: 'usd',
            product_data: {
              name: 'Producto Ejemplo',
            },
            unit_amount: 2000, // Precio en centavos ($20.00)
          },
          quantity: 1,
        },
      ],
      success_url: 'https://lite-chain.com/',
      cancel_url: 'https://kinderdoge.xyz/',
    });

    return {
      statusCode: 200,
      body: JSON.stringify({ id: session.id }),
    };
  } catch (error) {
    return { statusCode: 500, body: JSON.stringify({ error: error.message }) };
  }
};
