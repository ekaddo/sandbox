const express = require('express');
const { GoogleGenerativeAI } = require('@google/generative-ai');
require('dotenv').config();

const app = express();
const port = 3000;

app.use(express.static('public'));
app.use(express.json());

const genAI = new GoogleGenerativeAI(process.env.API_KEY);

app.post('/ask', async (req, res) => {
    const { question } = req.body;

    if (!question) {
        return res.status(400).json({ error: 'Question is required' });
    }

    try {
        const model = genAI.getGenerativeModel({ model: 'gemini-pro' });
        const result = await model.generateContent(question);
        const response = await result.response;
        const text = await response.text();
        res.json({ answer: text });
    } catch (error) {
        console.error(error);
        res.status(500).json({ error: 'Failed to get answer from Gemini API' });
    }
});

app.listen(port, () => {
    console.log(`Server listening at http://localhost:${port}`);
});
