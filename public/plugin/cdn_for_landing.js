document.addEventListener("DOMContentLoaded", function() {

    function normalizeUrl(url) {
        var rs_text =  url.indexOf('hocexcel.online/?') == -1 ? 'hocexcel.online?' : 'hocexcel.online/?';
        console.log(rs_text);
        return rs_text;
    }

    function getJsonFromUrl(url) {
        var query = url;
        var result = {};

        var param_urls =  query.split(normalizeUrl(url))[1];

        console.log(param_urls);

        if(typeof param_urls != 'undefined'){

            if(param_urls.indexOf('&') != -1){
                param_urls.split("&").forEach(function(part) {
                    var item = part.split("=");
                    result[item[0]] = decodeURIComponent(item[1]);
                });
            }
        }


        return result;
    }

    function getLandingName() {
        var full = window.location.host;
        var parts = full.split('.');
        var sub = parts[0];
        return sub;
    }


    function getMediumParams(utm_medium) {
        var arr = utm_medium.split('_');

        var medium_pars = {};

        if(arr[0] === 'FA' || arr[0] === 'GA' || arr[0] === 'GDN' || arr[0] === 'ADMARKET' || arr[0] === 'COCCOC'){
            medium_pars['channel'] = arr[0];
            medium_pars['code']    = arr[1];
            medium_pars['person'] = arr[2];
            medium_pars['type'] = arr[3];
            medium_pars['landing_page'] = arr[4];
            medium_pars['ads_group'] = arr[5];
            medium_pars['ads_id'] = arr[6];
        }

        if(arr[0] === 'SOCIAL' || arr[0] === 'YOUTUBE' || arr[0] === 'BLOG' || arr[0] === 'AF' || arr[0] === 'EMAIL'){
            medium_pars['channel'] = arr[0];
            medium_pars['code']    = arr[1];
            medium_pars['person'] = arr[2];
            medium_pars['type'] = arr[3];
            medium_pars['landing_page'] = arr[4];
        }

        return medium_pars;
    }


    var pars = getJsonFromUrl(window.location.href);


    var utm_source = document.createElement("input");
    utm_source.type = "hidden";
    utm_source.name = "label:utm-source";
    utm_source.value = "";

    var utm_medium = document.createElement("input");
    utm_medium.type = "hidden";
    utm_medium.name = "label:utm-medium";
    utm_medium.value =  "";

    var utm_campaign = document.createElement("input");
    utm_campaign.type = "hidden";
    utm_campaign.name = "label:utm-campaign";
    utm_campaign.value = "";

    var utm_term = document.createElement("input");
    utm_term.type = "hidden";
    utm_term.name = "label:utm-term";
    utm_term.value = "";

    var utm_content = document.createElement("input");
    utm_content.type = "hidden";
    utm_content.name = "label:utm-content";
    utm_content.value = "";

    // get landing sub
    var sub_landing = document.createElement("input");
    sub_landing.type = "hidden";
    sub_landing.name = "label:channel";
    sub_landing.value = getLandingName();


    // input marketing info
    var mkt_channel = document.createElement("input");
    mkt_channel.type = "hidden";
    mkt_channel.name = "label:channel";
    mkt_channel.value = "";

    var mkt_code = document.createElement("input");
    mkt_code.type = "hidden";
    mkt_code.name = "label:Code";
    mkt_code.value = "";

    var mkt_person = document.createElement("input");
    mkt_person.type = "hidden";
    mkt_person.name = "label:Person";
    mkt_person.value = "";

    var mkt_type = document.createElement("input");
    mkt_type.type = "hidden";
    mkt_type.name = "label:Type";
    mkt_type.value = "";

    var mkt_landing_page = document.createElement("input");
    mkt_landing_page.type = "hidden";
    mkt_landing_page.name = "label:LandingPage";
    mkt_landing_page.value = "";

    var mkt_ads_group = document.createElement("input");
    mkt_ads_group.type = "hidden";
    mkt_ads_group.name = "label:AdsGroup";
    mkt_ads_group.value = "";

    var mkt_ads_id = document.createElement("input");
    mkt_ads_id.type = "hidden";
    mkt_ads_id.name = "label:AdsID";
    mkt_ads_id.value = "";

    var mkt_landing_url = document.createElement("input");
    mkt_landing_url.type = "hidden";
    mkt_landing_url.name = "label:LandingPageURL";
    mkt_landing_url.value = window.location.href;



    if(pars){

        if(typeof pars.utm_source !== 'undefined'){
            utm_source.value = pars.utm_source;
        }

        if(typeof pars.utm_medium !== 'undefined'){
            utm_medium.value =  pars.utm_medium;

            // parser utm-medium here!
            var utm_medium_params = getMediumParams(pars.utm_medium);

            if(typeof utm_medium_params.channel !== 'undefined')
                mkt_channel.value = utm_medium_params.channel;

            if(typeof utm_medium_params.code!== 'undefined')
                mkt_code.value = utm_medium_params.code;

            if(typeof utm_medium_params.person !== 'undefined')
                mkt_person.value = utm_medium_params.person;

            if(typeof utm_medium_params.type !== 'undefined')
                mkt_type.value = utm_medium_params.type;

            if(typeof utm_medium_params.landing_page !== 'undefined')
                mkt_landing_page.value = utm_medium_params.landing_page;

            if(typeof utm_medium_params.ads_group !== 'undefined')
                mkt_ads_group.value = utm_medium_params.landing_page;

            if(typeof utm_medium_params.ads_id!== 'undefined')
                mkt_ads_id.value = utm_medium_params.ads_id;

        }

        if(typeof pars.utm_campaign !== 'undefined'){
            utm_campaign.value = pars.utm_campaign;
        }

        if(typeof pars.utm_term !== 'undefined'){
            utm_term.value = pars.utm_term;
        }

        if(typeof pars.utm_content !== 'undefined'){
            utm_content.value = pars.utm_content;
        }
    }

    console.log('append utm form');
    var my_form = document.getElementsByClassName("my_form")[0];
    my_form.appendChild(utm_source);
    my_form.appendChild(utm_medium);
    my_form.appendChild(utm_campaign);
    my_form.appendChild(utm_term);
    my_form.appendChild(utm_content);
    my_form.appendChild(sub_landing);
    my_form.appendChild(mkt_channel);
    my_form.appendChild(mkt_code);
    my_form.appendChild(mkt_person);
    my_form.appendChild(mkt_type);
    my_form.appendChild(mkt_landing_page);
    my_form.appendChild(mkt_ads_group);
    my_form.appendChild(mkt_ads_id);
    my_form.appendChild(mkt_landing_url);

});