package igam;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.util.ArrayList;
import java.util.logging.Level;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

class RunCmd {
	Runtime r = null;
	InputStreamReader isr = null;
	BufferedReader br = null;
	Process p =null;
	ArrayList<String> ipsetSSH = new ArrayList<String>();
	ArrayList<String> ipsetFTP = new ArrayList<String>();
	ArrayList<String> ipsetHTTP = new ArrayList<String>();
	ArrayList<String> ipsetSMTP = new ArrayList<String>();
    String regex = "\\A(25[0-5]|2[0-4]\\d|[0-1]?\\d?\\d)";
    Pattern pattern = null;
	
    /**
     * Constructor
     * Read all ipset rules from Debian system
     * 1. mynetrulesssh
     * 2. mynetrulesftp
     * 1. mynetruleshttp
     * 1. mynetrulessmtp
     * @throws IOException
     */
	public RunCmd() throws IOException {
		BlockIP.myLog.log(Level.INFO, "Creating RunCmd instance to Read IP SET");
		r = Runtime.getRuntime();
        pattern = Pattern.compile(regex);
		
		ipsetSSH = readIpset("/sbin/ipset list mynetrulesssh");
		ipsetFTP = readIpset("/sbin/ipset list mynetrulesftp");
		ipsetHTTP = readIpset("/sbin/ipset list mynetruleshttp");
		ipsetSMTP = readIpset("/sbin/ipset list mynetrulessmtp");
	}

	/**
	 * Method to read ipset
	 * called from contructor
	 * @param mCmd linux command to execute /sbin/ipset
	 * @return ArrayList<String>
	 * @throws IOException
	 */
	ArrayList<String> readIpset(String mCmd) throws IOException {
		ArrayList<String> mTemp = new ArrayList<String>();
		Process p = r.exec(mCmd);
        
        // from output stream
		isr = new InputStreamReader(p.getInputStream());
        br = new BufferedReader(isr);
        String tmp="";
        while ((tmp = br.readLine()) != null) {
			Matcher matcher = pattern.matcher(tmp);
            if (matcher.find()) {
                mTemp.add(tmp);
            } 
        }
        
        // from error stream
        isr = new InputStreamReader(p.getErrorStream());
        br = new BufferedReader(isr);
        tmp="";
        while ((tmp = br.readLine()) != null) {
        	//System.out.println(BlockIP.sdf.format(BlockIP.now)+" "+tmp);
			//BlockIP.myLog.log(Level.INFO, mCmd);

        }
        
        // waitFor() method is used to wait till the process returns the exit value
        try {
        	int exitValue = p.waitFor();
        	BlockIP.myLog.log(Level.INFO, "Exit Value is " + exitValue);
        } catch (InterruptedException ex) {
        	BlockIP.myLog.log(Level.INFO, "InterruptedException: "+ex.getMessage());
        }
		return mTemp;
	}
	
	/**
	 * 
	 * @param lIpset
	 * @param lMySQL
	 * @param ipsetName
	 * @throws IOException
	 */
	void execute(ArrayList<String> lIpset, ArrayList<String> lMySQL, String ipsetName ) throws IOException {
		String mCmd="";
		String tmp="";
		String[] mTempRules = lIpset.toArray(new String[0]);
		String[] mTempNew = lMySQL.toArray(new String[0]);
		for (int i=0;i<mTempNew.length;i++) {
			boolean addNew = true;
			String[] newIP = mTempNew[i].split("[.]");
        	String subNewIP="";
        	for (int j=0;j<3;j++) {
        		subNewIP=subNewIP+newIP[j]+".";
        	}
        	for (int j=0;j<mTempRules.length;j++) {
        		if (mTempRules[j].equals(mTempNew[i])) {
        			BlockIP.myLog.log(Level.SEVERE, mTempRules[j]+" already added");
        			addNew = false;
        		} else if (mTempRules[j].indexOf(subNewIP)>=0) {
        			BlockIP.myLog.log(Level.INFO, Integer.toString(mTempRules[j].indexOf("/")));
        			addNew = false;
        			if (mTempRules[j].indexOf("/")>=0){
        				BlockIP.myLog.log(Level.INFO, mTempNew[i]+" & "+mTempRules[j]+" already added in subnet: "+subNewIP);
        			} else {
        				BlockIP.myLog.log(Level.INFO, mTempNew[i]+" & "+mTempRules[j]+" has subnet: "+subNewIP);
        				mCmd = "/sbin/ipset del "+ipsetName+" "+mTempRules[j];
        				System.out.println(mCmd);
        				BlockIP.myLog.log(Level.INFO, mCmd);
        				p = r.exec(mCmd);
        				isr = new InputStreamReader(p.getInputStream());
        				br = new BufferedReader(isr);
        				tmp="";
        				while ((tmp = br.readLine()) != null) {
        					BlockIP.myLog.log(Level.INFO, tmp);
        				}
        				isr = new InputStreamReader(p.getErrorStream());
        				br = new BufferedReader(isr);
        				tmp="";
        				while ((tmp = br.readLine()) != null) {
        					BlockIP.myLog.log(Level.INFO, tmp);
        				}
        				// waitFor() method is used to wait till the process returns the exit value
        				try {
        					int exitValue = p.waitFor();
        					BlockIP.myLog.log(Level.INFO, "Exit Value is " + exitValue);
        				} catch (InterruptedException ex) {
        					BlockIP.myLog.log(Level.INFO, "InterruptedException: "+ex.getMessage());
        				}
        				mCmd = "/sbin/ipset add "+ipsetName+" "+subNewIP+"0/24";
        				//System.out.println(mCmd);
        				BlockIP.myLog.log(Level.INFO, mCmd);
        				p = r.exec(mCmd);
        				isr = new InputStreamReader(p.getInputStream());
        				br = new BufferedReader(isr);
        				tmp="";
        				while ((tmp = br.readLine()) != null) {
        					BlockIP.myLog.log(Level.INFO, tmp);
        				}
        				isr = new InputStreamReader(p.getErrorStream());
        				br = new BufferedReader(isr);
        				tmp="";
        				while ((tmp = br.readLine()) != null) {
        					BlockIP.myLog.log(Level.INFO, tmp);
        				}
        				// waitFor() method is used to wait till the process returns the exit value
        				try {
        					int exitValue = p.waitFor();
        					BlockIP.myLog.log(Level.INFO, "Exit Value is " + exitValue);
        				} catch (InterruptedException ex) {
        					BlockIP.myLog.log(Level.SEVERE, "InterruptedException: "+ex.getMessage());
        				}
        			}
        		}
        	}
        	//
        	if (addNew) {
        		mCmd = "/sbin/ipset add "+ipsetName+" "+mTempNew[i];
				System.out.println(mCmd);
				BlockIP.myLog.log(Level.INFO, mCmd);
        		p = r.exec(mCmd);
        		isr = new InputStreamReader(p.getInputStream());
        		br = new BufferedReader(isr);
        		tmp="";
        		while ((tmp = br.readLine()) != null) {
        			BlockIP.myLog.log(Level.INFO, tmp);
        		}
        		isr = new InputStreamReader(p.getErrorStream());
        		br = new BufferedReader(isr);
        		tmp="";
        		while ((tmp = br.readLine()) != null) {
        			BlockIP.myLog.log(Level.INFO, tmp);
        		}
        		// waitFor() method is used to wait till the process returns the exit value
        		try {
        			int exitValue = p.waitFor();
        			BlockIP.myLog.log(Level.INFO, "Exit Value is " + exitValue);
        		} catch (InterruptedException ex) {
        			BlockIP.myLog.log(Level.INFO, "InterruptedException: "+ex.getMessage());
        		}

        	}
		}
	}
}
