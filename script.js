function toggleChat()
{
    document.getElementById("chatbot").classList.toggle("hidden");
}

// Convert markdown to HTML for better formatting with proper spacing
function markdownToHTML(text, format = 'mixed') {
    // Normalize line endings
    text = text.replace(/\r\n/g, '\n');
    
    // Split by double newlines to identify paragraphs/sections
    const sections = text.split(/\n\n+/);
    let result = [];
    
    for (let section of sections) {
        section = section.trim();
        if (!section) continue;
        
        // Bold text **text** → <strong>text</strong>
        section = section.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
        
        // Italic text *text* → <em>text</em>
        section = section.replace(/\*(.*?)\*/g, '<em>$1</em>');
        
        // Check if this section contains bullet points
        const lines = section.split('\n');
        let hasBullets = lines.some(line => line.trim().startsWith('•') || line.trim().startsWith('-') || line.trim().startsWith('+'));
        let hasNumbers = lines.some(line => /^\d+\./.test(line.trim()));
        
        if (hasBullets || hasNumbers) {
            // Process as list
            let listHTML = [];
            let inList = false;
            
            for (let line of lines) {
                line = line.trim();
                
                if (line.startsWith('•') || line.startsWith('-')) {
                    if (!inList) {
                        listHTML.push('<ul class="list-disc list-inside space-y-1">');
                        inList = true;
                    }
                    listHTML.push('<li class="ml-2">' + line.replace(/^[•\-]\s*/, '') + '</li>');
                } else if (line.startsWith('+')) {
                    if (!inList) {
                        listHTML.push('<ul class="list-disc list-inside space-y-1">');
                        inList = true;
                    }
                    listHTML.push('<li class="ml-2">' + line.replace(/^\+\s*/, '') + '</li>');
                } else if (/^\d+\./.test(line)) {
                    if (!inList) {
                        listHTML.push('<ol class="list-decimal list-inside space-y-1">');
                        inList = true;
                    }
                    listHTML.push('<li class="ml-2">' + line.replace(/^\d+\.\s*/, '') + '</li>');
                } else if (line) {
                    if (inList) {
                        listHTML.push('</ul>');
                        inList = false;
                    }
                    listHTML.push('<p class="mb-2">' + line + '</p>');
                }
            }
            
            if (inList) {
                listHTML.push('</ul>');
            }
            
            result.push('<div class="mb-3">' + listHTML.join('') + '</div>');
        } else {
            // Process as paragraph or regular text
            result.push('<div class="mb-3 leading-relaxed">' + section + '</div>');
        }
    }
    
    return result.join('');
}

function sendMessage()
{
    const input = document.getElementById("userInput");
    const chatbox = document.getElementById("chatbox");
    
    let message = input.value.trim();
    
    if(message==="") return;
    
    // User message
    chatbox.innerHTML += `
    <div class="text-right mb-3">
        <span class="bg-emerald-500 text-black px-4 py-3 rounded-2xl inline-block max-w-xs md:max-w-sm text-base">
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
        let responseHTML = '';
        let format = data.format || 'mixed';
        
        // Check if fitness related
        if (data.isFitnessRelated === false) {
            // Non-fitness question response
            responseHTML = `
            <div class="text-left mb-3">
                <div class="bg-slate-700 px-4 py-3 rounded-2xl inline-block max-w-sm md:max-w-md text-base leading-relaxed">
                    ${markdownToHTML(data.response, 'points')}
                </div>
            </div>
            `;
        } else {
            // Fitness question response with segmented format
            responseHTML = `
            <div class="text-left mb-3">
                <div class="bg-slate-700 px-4 py-3 rounded-2xl inline-block max-w-2xl text-base">
                    ${markdownToHTML(data.response, format)}
                </div>
            </div>
            `;
            
            // Show detected keywords if available
            if (data.detectedKeywords && data.detectedKeywords.length > 0) {
                responseHTML += `
                <div class="text-left text-sm text-gray-400 mb-3 px-2">
                    Keywords detected: ${data.detectedKeywords.slice(0, 3).join(', ')}${data.detectedKeywords.length > 3 ? '...' : ''}
                </div>
                `;
            }
        }
        
        chatbox.innerHTML += responseHTML;
        chatbox.scrollTop = chatbox.scrollHeight;
    })
    
    .catch(()=>
    {
        chatbox.innerHTML += `
        <div class="text-left mb-3">
            <span class="bg-red-500 px-4 py-3 rounded-2xl inline-block text-base">
            Server error - Please try again
            </span>
        </div>
        `;
    });
    
    input.value="";
}