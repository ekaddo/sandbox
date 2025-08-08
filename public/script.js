document.getElementById('question-form').addEventListener('submit', async function(event) {
    event.preventDefault();

    const questionInput = document.getElementById('question-input');
    const question = questionInput.value;

    if (!question) {
        alert('Please enter a question.');
        return;
    }

    const answerElement = document.getElementById('answer');
    answerElement.textContent = 'Thinking...';

    try {
        const response = await fetch('/ask', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ question })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        answerElement.textContent = data.answer;

    } catch (error) {
        console.error('Error:', error);
        answerElement.textContent = 'Failed to get an answer. Please check the console for more details.';
    }
});
