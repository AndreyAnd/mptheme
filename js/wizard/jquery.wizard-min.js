    function wizardSkip(e,number)
    {   
        var form=$(e).up('form');
        var div=form.select('div[data-number="'+number+'"]')[0];
        var span_count=form.select('span[class="wizards_count"]')[0];
        var wizards_count=span_count.getAttribute("data-count");
        if( number<(wizards_count-1) )
        {
            div.classList.add("none");
            number++;
            div.nextSibling.classList.remove("none");;
            
        }
    }
    
    function wizardPrevios(e,number)
    {  
       var form=$(e).up('form');
        var div=form.select('div[data-number="'+number+'"]')[0];
        var span_count=form.select('span[class="wizards_count"]')[0];
        var wizards_count=span_count.getAttribute("data-count");
        if( number>0 )
        {
            div.classList.add("none");
            number--;
            div.previousSibling.classList.remove("none");;
            
        }
        
    }
    
    function wizardNext(e,number) 
    {  
        var form=$(e).up('form');
        var div=$(e).up('div');
        var span_count=form.select('span[class="wizards_count"]')[0];
        var wizards_count=span_count.getAttribute("data-count");
        if( number<(wizards_count-1) )
        {
            div.classList.add("none");
            number++; 
            div.nextSibling.classList.remove("none");;
            
        }
        
        //$$('div.myclass')[0];
              
    }
    

