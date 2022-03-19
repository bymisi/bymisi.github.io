@echo off
    IF EXIST DEF.XPSETUP (
        echo Previous Setup Files are DEF
        GOTO done
    ) ELSE (
        echo Finding Previous Setup Files...
    )
    IF EXIST 2010.XPSETUP (
        echo Previous Setup Files are 2010
	ren $WIN_NT$.~LS 2010$WIN_NT$.~LS
	ren $WIN_NT$.~BT 2010$WIN_NT$.~BT
	ren TXTSETUP.SIF 2010TXTSETUP.SIF
	ren NTLDR 2010NTLDR
	ren menu.lst 2010menu.lst

	ren DEF$WIN_NT$.~LS $WIN_NT$.~LS
	ren DEF$WIN_NT$.~BT $WIN_NT$.~BT
	ren DEFtxtsetup.sif txtsetup.sif
	ren DEFNTLDR NTLDR
	ren DEFmenu.lst menu.lst
	ren *.XPSETUP DEF.XPSETUP
        GOTO done
    ) ELSE (
        echo.
    )
    IF EXIST HUN.XPSETUP (
        echo Previous Setup Files are HUN
	ren $WIN_NT$.~LS HUN$WIN_NT$.~LS
	ren $WIN_NT$.~BT HUN$WIN_NT$.~BT
	ren TXTSETUP.SIF HUNTXTSETUP.SIF
	ren NTLDR HUNNTLDR
	ren XATSP HUNXATSP 
	ren XPSTP HUNXPSTP 
	ren menu.lst HUNmenu.lst

	ren DEF$WIN_NT$.~LS $WIN_NT$.~LS
	ren DEF$WIN_NT$.~BT $WIN_NT$.~BT
	ren DEFtxtsetup.sif txtsetup.sif
	ren DEFNTLDR NTLDR
	ren DEFXATSP XATSP
	ren DEFXPSTP XPSTP
	ren DEFmenu.lst menu.lst
	ren *.XPSETUP DEF.XPSETUP
        GOTO done
    ) ELSE (
        echo.
    )
GOTO error
:done
echo DONE
pause
GOTO END
:error
echo FILE IS MiSSiNG!!!
pause