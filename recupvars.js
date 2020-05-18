javascript: var tCQAvars = tCQAvars || {
    // recuperation des éléments récupérés dans le localstorage si présent
    tc_newvars: null != sessionStorage.getItem("QA_vars") ? JSON.parse(sessionStorage.getItem("QA_vars")) : {
        //declaration des variables extras, titres de la
        navigation: this.navigation || [],
        QA_category_name: this.QA_category_name || [],
        Launched_tags: this.Launched_tags || []
    },
    //verifie si l'element es null, un string ou un chiffre, ou un objet
    verifIfObj: function(a) {
        if (null == a) var b = "NULL";
        if ("string" == typeof a || !isNaN(a)) var b = a;
        if ("object" == typeof a) var b = this.getObject(a);
        return b
    },
    //stocke dans un objet et renvois vers verifIfObj de manière a passe en revue tous les objets
    getObject: function(a) {
        var b = {};
        for (var c in a) b[c] = this.verifIfObj(a[c]);
        return b
    },
    //fonction qui récupère tous les tc_vars
    getAllVars: function(a) {
        //on récupère la longueur du tableau de navigation
        for (var b = document.location.href, c = 1, d = 0; d < this.tc_newvars.navigation.length; d++) this.tc_newvars.navigation[d] == b && (c = 0);
        if (1 == c) {
            // on recupere le titre de la page, la liste des tags et les tc_vars qu'on stocke dans le tableau
            var f = document.title;
            if (this.tc_newvars.QA_category_name[d] = null != f ? f : "", this.tc_newvars.navigation[d] = b, this.tc_newvars.Launched_tags[d] = "undefined" != typeof tC.array_launched_tags ? "[\n" + tC.array_launched_tags.join(",\n") + "\n]" : "", "undefined" != typeof a)
                for (var g in a) {
                    var h = this.verifIfObj(a[g]);
                    "undefined" == typeof this.tc_newvars[g] && (this.tc_newvars[g] = {}), this.tc_newvars[g][d] = h
                }
        }
        // on sotcke tout dans le localstorage
        localStor = JSON.stringify(this.tc_newvars), sessionStorage.setItem("QA_vars", localStor)
    },
    //fonction d'envoi des données
    sendVars: function(a) {
        if (null !== a) {
            //creation de la requete ajax
            var b = new XMLHttpRequest;
            //definition de l'adresse d'envois de la requête et de l'adresse et écupération du fichier
            b.open("POST", "http://127.0.0.1/my%20portable%20files/export_tc_vars/index.php", !0), b.setRequestHeader("Content-type", "application/json"), b.onreadystatechange = function() {
                4 == b.readyState && (window.location = "http://127.0.0.1/my%20portable%20files/export_tc_vars/" + b.responseText)
            }, b.send(a)
        }
    },
    // suppresion du cookie
    stopQA: function() {
        tC.setCookie('StartQA', 0);
    },
    //pose du cookie et demarrage de l'enregistrement
    startQA: function() {
        tC.setCookie('StartQA', 1);
        this.getAllVars(tc_vars);

    }
};

if (tC.setCookie('StartQA') == 1) {
    tCQAvars.getAllVars(tc_vars);
}


javascript: tCQAvars.sendVars(sessionStorage.getItem("QA_vars"));



/************************************************
*************************************************
**** VIA SENDBEACON _ NEW VERSION CROSSDOMAIN ***
*************************************************
************************************************/
/*
1. recuperation des variables
2. Stockage des infos de navigation en localstorage
3. envois des tc_vars
*/




var getMyVars = function(MyVars) {
    //recuperation des informations de navigations
    var QAnav = QAnav || sessionStorage.getItem("QA_Nav") ? JSON.parse(sessionStorage.getItem("QA_Nav")) : {
            //declaration des variables extras, titres de la
            navigation: this.navigation||[]
        }
    }
    //envoi des données et enregistrement de la page si la page n'a pas encore été visitée
    var MyVar_url = document.location.href
    if(QAvars.navigation.indexOf(tc_url)==-1 ){
        QAnav.navigation.push(document.location.href);
        var MyVar_toSend = JSON.stringify(MyVars),
        navigator.sendBeacon("//preprod.tagcommander.com/~yann/QA/index.php", tc_toSend);
    }
}

// only register information if the cookie is present
if(document.cookie.match("MyVarsCookie=1")!=null){
    getMyVars(tc_vars);
}

//widget with navigation information