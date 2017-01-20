package igam;

import java.io.IOException;
import java.net.UnknownHostException;
import java.sql.SQLException;
import java.util.logging.FileHandler;
import java.util.logging.Level;
import java.util.logging.Logger;

public class BlockIP {
	
	protected static final Logger myLog = Logger.getLogger(BlockIP.class.getName());
	//private static java.util.GregorianCalendar todayDate;
	private static FileHandler fh;
	private static String fn = "/root/java/f2bblock.log"; // Fix file name

	public static void main(String[] args) {
		try {
	    	
			// Handling Logger
	    	//todayDate = new java.util.GregorianCalendar ();	
	    	fh = new FileHandler(fn, true); // true to append exisitng file
			java.util.logging.Formatter formatter = new BriefFormatter(); 
			fh.setFormatter(formatter);
			myLog.addHandler(fh);
	    	
			myLog.log(Level.INFO, "BlockIP start");

			IPFromMySQL ipFSQL = new IPFromMySQL();
			myLog.log(Level.INFO, "Reading MySQL");
			myLog.log(Level.INFO, "MySQL SSH: "+ipFSQL.mSSH.size());
			myLog.log(Level.INFO, "MySQL FTP: "+ipFSQL.mFTP.size());
			myLog.log(Level.INFO, "MySQL HTTP: "+ipFSQL.mHTTP.size());
			myLog.log(Level.INFO, "MySQL SMTP: "+ipFSQL.mSMTP.size());
			
			RunCmd rCmd = new RunCmd();
			
			myLog.log(Level.INFO, "ipset SSH: "+rCmd.ipsetSSH.size());
			myLog.log(Level.INFO, "ipset FTP: "+rCmd.ipsetFTP.size());
			myLog.log(Level.INFO, "ipset HTTP: "+rCmd.ipsetHTTP.size());
			myLog.log(Level.INFO, "ipset SMTP: "+rCmd.ipsetSMTP.size());
			
			rCmd.execute(rCmd.ipsetSSH, ipFSQL.mSSH, "mynetrulesssh");
			rCmd.execute(rCmd.ipsetFTP, ipFSQL.mFTP, "mynetrulesftp");
			rCmd.execute(rCmd.ipsetHTTP, ipFSQL.mHTTP, "mynetruleshttp");
			rCmd.execute(rCmd.ipsetSMTP, ipFSQL.mSMTP, "mynetrulessmtp");

		} catch (ClassNotFoundException e) {
			myLog.log(Level.SEVERE, "ClassNotFoundException: "+e.getMessage());
		} catch (UnknownHostException e) {
			myLog.log(Level.SEVERE, "UnknownHostException: "+e.getMessage());
		} catch (SQLException e) {
			myLog.log(Level.SEVERE, "SQLException: "+e.getMessage());
		} catch (IOException e) {
			myLog.log(Level.SEVERE, "IOException: "+e.getMessage());
		}
		myLog.log(Level.INFO, "BlockIP end");
	}
}
