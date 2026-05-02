function toggleChat()
{
    document.getElementById("chatbot").classList.toggle("hidden");
}

function sendMessage()
{
    const input = document.getElementById("userInput");

    const chatbox = document.getElementById("chatbox");

    let message = input.value.trim();

    if(message==="") return;

    // User message
    chatbox.innerHTML += `
    <div class="text-right">
        <span class="bg-emerald-500 text-black px-3 py-1 rounded-lg inline-block">
        ${message}
        </span>
    </div>
    `;

    fetch("chat.php",
    {
        method:"POST",

        headers:
        {
            "Content-Type":"application/x-www-form-urlencoded"
        },

        body:"message="+encodeURIComponent(message)
    })

    .then(res=>res.json())

    .then(data=>
    {
        chatbox.innerHTML += `
        <div class="text-left">
            <span class="bg-slate-700 px-3 py-1 rounded-lg inline-block">
            ${data.response}
            </span>
        </div>
        `;

        chatbox.scrollTop = chatbox.scrollHeight;
    })

    .catch(()=>
    {
        chatbox.innerHTML += `
        <div class="text-left">
            <span class="bg-red-500 px-3 py-1 rounded">
            Server error
            </span>
        </div>
        `;
    });

    input.value="";
}