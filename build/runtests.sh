#!/usr/bin/env bash
#
# =============================================================================
#title:         runtests.sh
#description:   Führt die Tests der Softwate aus
#author:        pfroch <patrick.froch@easySolutionsIT.de>
#date:          20231027
#version:       1.1.0
#usage:         runtests.sh
# =============================================================================
#


## Ausgabe
function myecho() {
    if [ "${VERBOSE}" == "TRUE" ]
    then
        echo -e "\e[1;96m\n================================================================================"
        echo -e "${1}"
        echo -e "--------------------------------------------------------------------------------\n\e[0m"
    fi
}

function myinfo() {
    if [ "${VERBOSE}" == "TRUE" ]
    then
        echo -e "\e[0;37m\n================================================================================"
        echo -e "${1}"
        echo -e "--------------------------------------------------------------------------------\n\e[0m"
    fi
}

function myerror() {
    if [ "${VERBOSE}" == "TRUE" ]
    then
        echo -e "\n\e[1;91m================================================================================\e[0m"
        echo -e "\e[0;101m\u2717 ${1}\e[0m"
        echo -e "\e[1;91m--------------------------------------------------------------------------------\e[0m"
    else
        echo -e "\e[0;101m\u2717 ${1}\e[0m"
    fi
}

function myshortecho() {
    if [ "${VERBOSE}" != "TRUE" ]
    then
        echo -e "\e[0;92m\u2713 ${1}\e[0m"
    fi
}


## Variablen
error=0
tmperr=0
configFolder='./build'
toolFolder="${configFolder}/tools"
classesFolder='./Classes'
EXTENDED="TRUE"
PHP="php"
FIX=""

if [ -f $HOME/bin/php ]
then
    PHP="$HOME/bin/php"
fi


##
# Parameters
##
while [ $# -gt 0 ]
do
    case ${1} in
    -v|--verbose)
        VERBOSE="TRUE"
        #shift  # Kein shift, da kein Wert übergeben wird!
        ;;

    -e|--extended)
        EXTENDED="TRUE" # Bis auf Weiteres immer true! Wenn nicht, Zeile 92 entfernen!
        #shift  # Kein shift, da kein Wert übergeben wird!
        ;;

  -f|--fix)
        FIX="--fix"
        #shift  # Kein shift, da kein Wert übergeben wird!
        ;;

    -e|--extended)
        EXTENDED="TRUE" # Bis auf Weiteres immer true! Wenn nicht, Zeile 92 entfernen!
        #shift  # Kein shift, da kein Wert übergeben wird!
        ;;

    *)          # unknown option
        myerror "Parameter [${1}] unbekannt!"
        #shift  # Kein shift, da kein Wert übergeben wird!
        ;;
    esac
    shift
done


##
# Header
#
echo -e "\e[1;96m\n================================================================================"
echo -e "e@sy Solutions IT - Test Suite by Patrick Froch - Version: 1.0.1"
echo -e "--------------------------------------------------------------------------------"
echo -n "PHP-Version: "
$PHP -v | grep ^PHP | cut -d' ' -f2 | cut -d'-' -f1
echo -e "\n\e[0m"


## phpcs
if [ -f ${toolFolder}/phpcs ]
then
    myecho "Führe statische Code-Analyse mit PHP Codesniffer durch"
    if [ "${VERBOSE}" == "TRUE" ]
    then
        ${PHP} ${toolFolder}/phpcs --colors --standard=PSR2 ${classesFolder}
        tmperr=$?
    else
        ${PHP} ${toolFolder}/phpcs -q --standard=PSR2 ${classesFolder}
        tmperr=$?
    fi

    if [ ${tmperr} -ne 0 ]
    then
        error=${tmperr}
        myerror "Es ist ein Fehler ausgetreten [${tmperr}]"
    else
        myshortecho "Statische Code-Analyse mit PHP Codesniffer erfolgreich"
    fi
else
    myinfo "Statische Code-Analyse ausgelassen. PHP Codesniffer nicht vorhanden!"
fi


## phpcpd
if [ -f ${toolFolder}/phpcpd ]
then
    myecho "Prüfe auf doppelten Code"
    if [ "${VERBOSE}" == "TRUE" ]
    then
        ${PHP} ${toolFolder}/phpcpd ${classesFolder}
        tmperr=$?
    else
        ${PHP} ${toolFolder}/phpcpd ${classesFolder} &>/dev/null
        tmperr=$?
    fi

    if [ ${tmperr} -ne 0 ]
    then
        error=${tmperr}
        myerror "Bei der Prüfung auf doppelten Code ist ein Fehler ausgetreten [${tmperr}]"
    else
       myshortecho "Prüfung auf doppelten Code erfolgreich"
    fi
else
    myinfo "Prüfen auf doppelten Code ausgelassen. PhpCopyAndPasteDetector nicht vorhanden!"
fi


## Easy Coding Standard
if [ -f ../../../vendor/bin/ecs ] && [ "TRUE" == "${EXTENDED}" ]
then
    myecho "Prüfe Code-Style mit Easy Coding Standard"
    if [ "${VERBOSE}" == "TRUE" ]
    then
        ${PHP} ../../../vendor/bin/ecs ${FIX} --config=build/ecs.php
        tmperr=$?
    else
        ${PHP} ../../../vendor/bin/ecs ${FIX} -q --config=build/ecs.php
        tmperr=$?
    fi

    if [ ${tmperr} -ne 0 ]
    then
        error=${tmperr}
        myerror "Es ist ein Fehler ausgetreten [${tmperr}]"
    fi
else
    myinfo "Prüfen des Code-Style mit Easy Coding Standard ausgelassen. ecs nicht vorhanden!"
fi


## PHPStan
if [ -f "${toolFolder}/phpstan" ] && [ "TRUE" == "${EXTENDED}" ]
then
    myecho "Prüfe Code-Qualität mit PHPStan"

    if [ "${VERBOSE}" == "TRUE" ]
    then
        "${PHP} ${toolFolder}/phpstan" analyse -c "${configFolder}/phpstan.neon"
        tmperr=$?
    else
        "${PHP} ${toolFolder}/phpstan" analyse -q -c "${configFolder}/phpstan.neon"
        tmperr=$?
    fi

    if [ ${tmperr} -ne 0 ]
    then
        error=${tmperr}
        "${toolFolder}/phpstan" analyse -c "${configFolder}/phpstan.neon"
    else
       myshortecho "Prüfen der Code-Qualität mit PHPStan erfolgreich"
    fi
else
    myinfo "Prüfen der Code-Qualität mit PHPStan ausgelassen. PHPStan nicht vorhanden!"
fi


## PHPUnit
if [ -f ../../../vendor/bin/phpunit ] && [ -d ./Tests ]
then
    # PHPUnit gobal mit composer installiert
    echo
    myecho "Führe UnitTests mit globalem PHPUnit durch"
    XDEBUG_MODE=coverage ${PHP} ../../../vendor/bin/phpunit --configuration ${configFolder}/phpunit/phpunit.xml.dist --testdox
    tmperr=$?

    if [ ${tmperr} -ne 0 ]
    then
        error=${tmperr}
        myerror "Es ist ein Fehler ausgetreten [${tmperr}]"
    fi
else
    myinfo "Ausführen der UnitTests ausgelassen. PHPUnit nicht vorhanden!"
fi

echo


## Zusammenfassung
if [ ${error} -ne 0 ]
then
    if [ "${VERBOSE}" != "TRUE" ]
    then
        echo
    fi

    myerror ">>>>>>>>>> Bei der Verarbeitung der Tests sind Fehler aufgetreten ! <<<<<<<<<<"
    echo
    exit 127
else

    myecho ">>>>>>>>>>>>>>>>>>>>>>> Es sind keine Fehler aufgetreten <<<<<<<<<<<<<<<<<<<<<<<"
    echo
    exit 0
fi


